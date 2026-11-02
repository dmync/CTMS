<?php
require_once __DIR__ . "/RandomSelector.php";

class MatchEngine {

    // ---- Utility: get team players ordered ----
    public static function getTeamPlayers($conn, $team_id) {
        $players = [];
        $q = $conn->query("SELECT id,name FROM players WHERE team_id=$team_id ORDER BY id ASC");
        while($r = $q->fetch_assoc()) $players[] = $r;
        return $players; // array of [id,name]
    }

    // ---- Helper: choose batting/bowling teams from matches row (supports multiple schemas) ----
    private static function pickTeamsFromMatchRow($m): array {
        // New schema
        $bat  = (int)($m['batting_team_id'] ?? 0);
        $bowl = (int)($m['bowling_team_id'] ?? 0);
        // Old schema fallback
        if ($bat <= 0)  $bat  = (int)($m['team_batting'] ?? 0);
        if ($bowl <= 0) $bowl = (int)($m['team_bowling'] ?? 0);
        return [$bat, $bowl];
    }

    // ---- Helper: ensure batting_score rows exist for all batting players ----
    private static function ensureBattingRows($conn, int $match_id, int $innings_no, int $batting_team_id): void {
        $batPlayers = self::getTeamPlayers($conn, $batting_team_id);
        foreach ($batPlayers as $p) {
            $pid = (int)$p['id'];
            $chk = $conn->query("SELECT id FROM batting_score WHERE match_id=$match_id AND innings_no=$innings_no AND player_id=$pid LIMIT 1");
            if ($chk && $chk->num_rows == 0) {
                $conn->query("INSERT INTO batting_score(match_id,innings_no,player_id) VALUES($match_id,$innings_no,$pid)");
            }
        }
    }

    // ---- Helper: ensure bowling_figures rows exist for all bowling players ----
    private static function ensureBowlingRows($conn, int $match_id, int $innings_no, int $bowling_team_id): void {
        $bowlPlayers = self::getTeamPlayers($conn, $bowling_team_id);
        foreach ($bowlPlayers as $p) {
            $pid = (int)$p['id'];
            $chk = $conn->query("SELECT id FROM bowling_figures WHERE match_id=$match_id AND innings_no=$innings_no AND player_id=$pid LIMIT 1");
            if ($chk && $chk->num_rows == 0) {
                $conn->query("INSERT INTO bowling_figures(match_id,innings_no,player_id) VALUES($match_id,$innings_no,$pid)");
            }
        }
    }

    /**
     * Ensure innings exists (create innings 1 if missing) and repair missing
     * striker/non_striker/current_bowler and missing batting/bowling rows.
     */
    public static function ensureInnings($conn, $match_id) {
        $m = $conn->query("SELECT * FROM matches WHERE id=$match_id")->fetch_assoc();
        if(!$m) return ["error"=>"Match not found"];

        [$bat, $bowl] = self::pickTeamsFromMatchRow($m);
        if ($bat <= 0 || $bowl <= 0) {
            return ["error"=>"Match teams not set. Start match / toss first."];
        }

        $inn1 = $conn->query("SELECT * FROM innings WHERE match_id=$match_id AND innings_no=1");
        if($inn1->num_rows == 0){
            $batPlayers  = self::getTeamPlayers($conn,$bat);
            $bowlPlayers = self::getTeamPlayers($conn,$bowl);
            if(count($batPlayers) < 2 || count($bowlPlayers) < 1){
                return ["error"=>"Not enough players to start"];
            }

            $striker = (int)$batPlayers[0]['id'];
            $non     = (int)$batPlayers[1]['id'];
            $bowler  = (int)$bowlPlayers[0]['id'];

            $conn->query("INSERT INTO innings(match_id,innings_no,batting_team_id,bowling_team_id,striker_id,non_striker_id,next_batsman_idx,current_bowler_id)
                          VALUES($match_id,1,$bat,$bowl,$striker,$non,3,$bowler)");
        }

        // Repair bad/empty state (common when old code created innings with zeros)
        $inn = $conn->query("SELECT * FROM innings WHERE match_id=$match_id AND innings_no=1 LIMIT 1")->fetch_assoc();
        if ($inn) {
            $batting_team_id = (int)($inn['batting_team_id'] ?? $bat);
            $bowling_team_id = (int)($inn['bowling_team_id'] ?? $bowl);

            $batPlayers  = self::getTeamPlayers($conn, $batting_team_id);
            $bowlPlayers = self::getTeamPlayers($conn, $bowling_team_id);
            if(count($batPlayers) < 2 || count($bowlPlayers) < 1){
                return ["error"=>"Not enough players to start"];
            }

            $needUpdate = false;
            $striker = (int)($inn['striker_id'] ?? 0);
            $non     = (int)($inn['non_striker_id'] ?? 0);
            $bowler  = (int)($inn['current_bowler_id'] ?? 0);

            if ($striker <= 0) { $striker = (int)$batPlayers[0]['id']; $needUpdate = true; }
            if ($non <= 0 || $non == $striker) { $non = (int)$batPlayers[1]['id']; $needUpdate = true; }
            if ($bowler <= 0) { $bowler = (int)$bowlPlayers[0]['id']; $needUpdate = true; }

            if ($needUpdate) {
                $conn->query("UPDATE innings
                              SET batting_team_id=$batting_team_id,
                                  bowling_team_id=$bowling_team_id,
                                  striker_id=$striker,
                                  non_striker_id=$non,
                                  current_bowler_id=$bowler
                              WHERE id=".(int)$inn['id']);
            }

            // Ensure score rows exist (so UI tables are never empty)
            self::ensureBattingRows($conn, (int)$match_id, 1, $batting_team_id);
            self::ensureBowlingRows($conn, (int)$match_id, 1, $bowling_team_id);
        }

        return ["status"=>"ok"];
    }

    // ---- Switch strike ----
    private static function swapStrike(&$inn) {
        $tmp = $inn['striker_id'];
        $inn['striker_id'] = $inn['non_striker_id'];
        $inn['non_striker_id'] = $tmp;
    }

    // ---- Convert balls to overs string ----
    public static function ballsToOvers($balls) {
        return floor($balls/6) . "." . ($balls%6);
    }

    // ---- Start innings 2 if innings 1 completed ----
    private static function startInnings2IfNeeded($conn, $match_id) {
        $inn1 = $conn->query("SELECT * FROM innings WHERE match_id=$match_id AND innings_no=1")->fetch_assoc();
        if(!$inn1) return;

        // If innings 1 completed and innings 2 not created yet
        if($inn1['status'] == 'Completed'){
            $inn2q = $conn->query("SELECT * FROM innings WHERE match_id=$match_id AND innings_no=2");
            if($inn2q->num_rows == 0){
                // Swap teams (support multiple schemas)
                $m = $conn->query("SELECT * FROM matches WHERE id=$match_id")->fetch_assoc();
                if(!$m) return;
                [$bat1, $bowl1] = self::pickTeamsFromMatchRow($m);
                $bat  = (int)$bowl1; // innings 2 batting = innings 1 bowling
                $bowl = (int)$bat1;
                $batPlayers = self::getTeamPlayers($conn,$bat);
                $bowlPlayers = self::getTeamPlayers($conn,$bowl);

                if(count($batPlayers) < 2 || count($bowlPlayers) < 1){
                    return; // cannot start innings 2
                }

                $striker = (int)$batPlayers[0]['id'];
                $non = (int)$batPlayers[1]['id'];
                $bowler = (int)$bowlPlayers[0]['id'];
                $target = (int)$inn1['total_runs'] + 1;

                $conn->query("INSERT INTO innings(match_id,innings_no,batting_team_id,bowling_team_id,striker_id,non_striker_id,next_batsman_idx,current_bowler_id,target)
                              VALUES($match_id,2,$bat,$bowl,$striker,$non,3,$bowler,$target)");

                // Ensure score rows exist
                self::ensureBattingRows($conn, (int)$match_id, 2, (int)$bat);
                self::ensureBowlingRows($conn, (int)$match_id, 2, (int)$bowl);
            }
        }
    }

    // ---- Generate one ball (or extra) and update all tables ----
    public static function generateNext($conn, $match_id) {
        // Ensure innings exists
        $ensure = self::ensureInnings($conn,$match_id);
        if(isset($ensure['error'])) return $ensure;

        // If innings 2 needs starting, do it
        self::startInnings2IfNeeded($conn,$match_id);

        // Current innings = first "In Progress"
        $inn = $conn->query("SELECT * FROM innings WHERE match_id=$match_id AND status='In Progress' ORDER BY innings_no ASC LIMIT 1")->fetch_assoc();
        if(!$inn){
            return ["status"=>"finished"];
        }

        $m = $conn->query("SELECT * FROM matches WHERE id=$match_id")->fetch_assoc();
        if(!$m) return ["error"=>"Match not found"];

        $overs_limit   = (int)($m['overs_limit'] ?? 0);

        // If DB does not have wickets_limit column, fallback to 10
        $wickets_limit = 10;
        if (is_array($m) && array_key_exists('wickets_limit', $m) && $m['wickets_limit'] !== null) {
            $wickets_limit = (int)$m['wickets_limit'];
            if ($wickets_limit <= 0) $wickets_limit = 10;
        }

        $legal_balls = (int)$inn['balls'];

        // ---- Stop innings conditions (SAFE) ----
        // Overs limit: only if overs_limit is valid (>0)
        if($overs_limit > 0){
            $maxBalls = $overs_limit * 6;
            if($legal_balls >= $maxBalls){
                $conn->query("UPDATE innings SET status='Completed' WHERE id=".(int)$inn['id']);
                self::startInnings2IfNeeded($conn,$match_id);

                $innCheck = $conn->query("SELECT id FROM innings WHERE match_id=$match_id AND status='In Progress'")->num_rows;
                if($innCheck == 0){
                    $conn->query("UPDATE matches SET status='Completed' WHERE id=$match_id");
                }
                return ["status"=>"innings_completed","reason"=>"overs_limit_reached"];
            }
        }

        // Wickets limit
        if((int)$inn['wickets'] >= $wickets_limit){
            $conn->query("UPDATE innings SET status='Completed' WHERE id=".(int)$inn['id']);
            self::startInnings2IfNeeded($conn,$match_id);

            $innCheck = $conn->query("SELECT id FROM innings WHERE match_id=$match_id AND status='In Progress'")->num_rows;
            if($innCheck == 0){
                $conn->query("UPDATE matches SET status='Completed' WHERE id=$match_id");
            }
            return ["status"=>"innings_completed","reason"=>"wickets_limit_reached"];
        }

        // If chasing target in innings 2
        if((int)$inn['innings_no'] == 2 && $inn['target'] !== null){
            if((int)$inn['total_runs'] >= (int)$inn['target']){
                $conn->query("UPDATE innings SET status='Completed' WHERE id=".(int)$inn['id']);
                $conn->query("UPDATE matches SET status='Completed' WHERE id=$match_id");
                return ["status"=>"chase_completed","reason"=>"target_reached"];
            }
        }

        // Event probabilities (simple but realistic)
        // First: decide if extra (WD/NB) occurs
        $type = RandomSelector::choose([
            "normal" => 92,
            "wide"   => 5,
            "noball" => 3
        ]);

        $runs_bat = 0;
        $runs_extra = 0;
        $extra_type = null;
        $wicket_type = null;
        $is_wicket = 0;
        $dismissed_player = null;

        $commentary = "";

        if($type == "wide"){
            $runs_extra = 1;
            $extra_type = "WD";
            $commentary = "Wide ball +1";
            // wide is not a legal ball
            self::applyDelivery($conn,$match_id,$inn,$runs_bat,$runs_extra,$extra_type,$is_wicket,$wicket_type,$dismissed_player,$commentary,false);
            return ["status"=>"ok","event"=>"WD"];
        }

        if($type == "noball"){
            $runs_extra = 1;
            $extra_type = "NB";
            // On no-ball, batsman may score off bat too (simple model)
            $batOutcome = RandomSelector::choose([
                "0"=>35,"1"=>25,"2"=>15,"4"=>18,"6"=>7
            ]);
            $runs_bat = (int)$batOutcome;
            $commentary = "No Ball +1, off bat ".$runs_bat;

            // no-ball not counted as legal
            self::applyDelivery($conn,$match_id,$inn,$runs_bat,$runs_extra,$extra_type,$is_wicket,$wicket_type,$dismissed_player,$commentary,false);

            // strike rotation if odd bat runs
            if($runs_bat % 2 == 1){
                $tmp = $inn['striker_id']; $inn['striker_id'] = $inn['non_striker_id']; $inn['non_striker_id'] = $tmp;
                $conn->query("UPDATE innings SET striker_id=".(int)$inn['striker_id'].", non_striker_id=".(int)$inn['non_striker_id']." WHERE id=".(int)$inn['id']);
            }
            return ["status"=>"ok","event"=>"NB"];
        }

        // Normal legal ball: decide wicket or runs
        $normal = RandomSelector::choose([
            "wicket" => 5,
            "runs"   => 95
        ]);

        if($normal == "wicket"){
            $is_wicket = 1;
            $wicket_type = RandomSelector::choose([
                "bowled"=>30,
                "caught"=>35,
                "lbw"=>20,
                "runout"=>10,
                "stumped"=>5
            ]);
            $dismissed_player = (int)$inn['striker_id'];
            $commentary = "WICKET! ".$wicket_type;

            self::applyDelivery($conn,$match_id,$inn,0,0,null,1,$wicket_type,$dismissed_player,$commentary,true);

            // bring next batsman if available
            $batPlayers = self::getTeamPlayers($conn,(int)$inn['batting_team_id']);
            $idx = (int)$inn['next_batsman_idx'] - 1; // 3 => index 2
            if($idx < count($batPlayers)){
                $newStriker = (int)$batPlayers[$idx]['id'];
                $inn['striker_id'] = $newStriker;
                $inn['next_batsman_idx'] = (int)$inn['next_batsman_idx'] + 1;
                $conn->query("UPDATE innings SET striker_id=$newStriker, next_batsman_idx=".(int)$inn['next_batsman_idx']." WHERE id=".(int)$inn['id']);
            } else {
                // all out
                $conn->query("UPDATE innings SET status='Completed' WHERE id=".(int)$inn['id']);
            }
            return ["status"=>"ok","event"=>"W"];
        }

        // Runs on legal ball
        $batOutcome = RandomSelector::choose([
            "0"=>35,"1"=>25,"2"=>15,"3"=>5,"4"=>15,"6"=>5
        ]);
        $runs_bat = (int)$batOutcome;
        $commentary = "Runs: ".$runs_bat;

        self::applyDelivery($conn,$match_id,$inn,$runs_bat,0,null,0,null,null,$commentary,true);

        // strike rotation on odd runs
        if($runs_bat % 2 == 1){
            $tmp = $inn['striker_id']; $inn['striker_id'] = $inn['non_striker_id']; $inn['non_striker_id'] = $tmp;
            $conn->query("UPDATE innings SET striker_id=".(int)$inn['striker_id'].", non_striker_id=".(int)$inn['non_striker_id']." WHERE id=".(int)$inn['id']);
        }

        // end of over: swap strike + rotate bowler
        $innAfter = $conn->query("SELECT * FROM innings WHERE id=".(int)$inn['id'])->fetch_assoc();
        if($innAfter && (((int)$innAfter['balls'] % 6) == 0)){
            // swap strike
            $conn->query("UPDATE innings SET striker_id=".(int)$innAfter['non_striker_id'].", non_striker_id=".(int)$innAfter['striker_id']." WHERE id=".(int)$innAfter['id']);

            // rotate bowler (simple round-robin)
            $bowlPlayers = self::getTeamPlayers($conn,(int)$innAfter['bowling_team_id']);
            if(count($bowlPlayers) > 0){
                $current = (int)$innAfter['current_bowler_id'];
                $pos = 0;
                for($i=0;$i<count($bowlPlayers);$i++){
                    if((int)$bowlPlayers[$i]['id'] == $current){ $pos = $i; break; }
                }
                $next = (int)$bowlPlayers[ ($pos+1) % count($bowlPlayers) ]['id'];
                $conn->query("UPDATE innings SET current_bowler_id=$next WHERE id=".(int)$innAfter['id']);
            }
        }

        return ["status"=>"ok","event"=>"BALL"];
    }

    // ---- Apply delivery to DB + update scorecards ----
    private static function applyDelivery($conn,$match_id,$inn,$runs_bat,$runs_extra,$extra_type,$is_wicket,$wicket_type,$dismissed_player,$commentary,$is_legal) {
        $match_id = (int)$match_id;
        $innings_no = (int)$inn['innings_no'];
        $striker = (int)$inn['striker_id'];
        $bowler  = (int)$inn['current_bowler_id'];

        // Get current balls from DB (more reliable than passed $inn)
        $innDb = $conn->query("SELECT id, balls FROM innings WHERE id=".(int)$inn['id']." LIMIT 1")->fetch_assoc();
        $ballsBefore = $innDb ? (int)$innDb['balls'] : (int)$inn['balls'];

        // Calculate over/ball numbers (best-effort)
        // For legal balls, next legal delivery number = ballsBefore + 1
        // For extras (non-legal), keep over/ball as current (or 0)
        $over_no = 0;
        $ball_no = 0;
        if($is_legal){
            $nextBall = $ballsBefore + 1;
            $over_no = (int)floor(($nextBall - 1) / 6) + 1; // 1-based over
            $ball_no = (int)(($nextBall - 1) % 6) + 1;      // 1..6
        } else {
            // same over as current ball position
            $over_no = (int)floor($ballsBefore / 6) + 1;
            $ball_no = (int)($ballsBefore % 6); // 0..5 (extra before next legal ball)
        }

        $totalRunsThisBall = (int)$runs_bat + (int)$runs_extra;

        // Insert ball row (deliveries)
        $extra_type_sql = $extra_type ? ("'".$conn->real_escape_string($extra_type)."'") : "NULL";
        $wicket_type_sql = $wicket_type ? ("'".$conn->real_escape_string($wicket_type)."'") : "NULL";
        $dismissed_sql = $dismissed_player ? (int)$dismissed_player : "NULL";
        $commentary_sql = $commentary ? ("'".$conn->real_escape_string($commentary)."'") : "NULL";

        // Note: "extra" column may exist (string). Keep NULL, we use extra_type.
        $conn->query(
            "INSERT INTO deliveries(
                match_id, over_no, ball_no, runs, is_wicket, extra,
                innings_no, striker_id, bowler_id,
                runs_bat, runs_extra, extra_type, wicket_type, dismissed_player_id, commentary
            ) VALUES(
                $match_id, $over_no, $ball_no, $totalRunsThisBall, ".(int)$is_wicket.", NULL,
                $innings_no, $striker, $bowler,
                ".(int)$runs_bat.", ".(int)$runs_extra.", $extra_type_sql, $wicket_type_sql, $dismissed_sql, $commentary_sql
            )"
        );

        // Update innings totals
        $addBalls = $is_legal ? 1 : 0;
        $conn->query("UPDATE innings
                      SET total_runs = total_runs + $totalRunsThisBall,
                          wickets = wickets + ".((int)$is_wicket).",
                          balls = balls + $addBalls
                      WHERE id=".(int)$inn['id']);

        // Update batting stats
        // For WD, batsman does NOT face ball. For NB, batsman does NOT get ball faced (academic rule)
        $addBatBall = ($is_legal ? 1 : 0);
        $conn->query("UPDATE batting_score
                      SET runs = runs + ".(int)$runs_bat.",
                          balls = balls + $addBatBall,
                          fours = fours + ".(($runs_bat==4)?1:0).",
                          sixes = sixes + ".(($runs_bat==6)?1:0)."
                      WHERE match_id=$match_id AND innings_no=$innings_no AND player_id=$striker");

        // Wicket handling
        if((int)$is_wicket == 1 && $dismissed_player){
            $outDesc = $wicket_type ? $wicket_type : "out";
            $conn->query("UPDATE batting_score
                          SET is_out=1, out_desc='".$conn->real_escape_string($outDesc)."'
                          WHERE match_id=$match_id AND innings_no=$innings_no AND player_id=".(int)$dismissed_player);
        }

        // Update bowling figures (legal balls only; runs include extras too)
        $conn->query("UPDATE bowling_figures
                      SET runs = runs + $totalRunsThisBall,
                          balls = balls + ".($is_legal?1:0).",
                          wickets = wickets + ".((int)$is_wicket)."
                      WHERE match_id=$match_id AND innings_no=$innings_no AND player_id=$bowler");
    }
}
