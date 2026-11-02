<?php
require_once "../core/Auth.php";
Auth::requireLogin();

$match_id = (int)($_GET['match_id'] ?? 0);
if($match_id <= 0){
    die("match_id missing. Open from Match List.");
}

$pageTitle = "Live Match";
$pageSub   = "Match #".$match_id." • Live scorecard updates";
include("partials/layout_top.php");
?>

<div class="card-ui mb-3">
  <div class="d-flex justify-content-between align-items-start">
    <div>
      <div class="small-muted mb-1">Live Score</div>
      <span id="liveStatus" class="badge bg-secondary">Idle</span>

      <h3 id="scoreLine" class="mb-0">Loading...</h3>
      <div class="small-muted mt-1">
        Use Auto Play for ball-by-ball updates or Refresh for latest state.
      </div>
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm icon-btn" href="match_list.php">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
  </div>

  <hr style="border-color: var(--border);">

  <div class="d-flex flex-wrap gap-2">
    <button class="btn btn-primary btn-sm icon-btn" onclick="startAutoPlay(<?= $match_id ?>)">
      <i class="bi bi-play-circle"></i> Auto Play
    </button>

    <button class="btn btn-outline-danger btn-sm icon-btn" onclick="stopAutoPlay()">
      <i class="bi bi-stop-circle"></i> Stop
    </button>

    <button id="btnRefresh" class="btn btn-outline-primary btn-sm icon-btn" disabled>
      <i class="bi bi-arrow-repeat"></i> Refresh
    </button>

    <span class="badge bg-secondary align-self-center">Match ID: <?= $match_id ?></span>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Batting</h5>
        <span class="badge badge-soft">Innings</span>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th>Player</th>
              <th>Status</th>
              <th style="width:70px;">R</th>
              <th style="width:70px;">B</th>
              <th style="width:70px;">4s</th>
              <th style="width:70px;">6s</th>
            </tr>
          </thead>
          <tbody id="batBody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card-ui mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Bowling</h5>
        <span class="badge badge-soft">Figures</span>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th>Bowler</th>
              <th style="width:80px;">O</th>
              <th style="width:80px;">R</th>
              <th style="width:80px;">W</th>
            </tr>
          </thead>
          <tbody id="bowlBody"></tbody>
        </table>
      </div>
    </div>

    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Last Balls</h5>
        <span class="badge badge-soft">Recent</span>
      </div>

      <ul class="list-group" id="lastBalls"></ul>

      <div class="small-muted mt-2">
        Shows last 6 deliveries from the live feed.
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/autoplay.js"></script>
<script>
  const mid = <?= $match_id ?>;

  const btn = document.getElementById("btnRefresh");
  btn.onclick = () => refreshLive(mid);

  fetch("../api/init_match.php?match_id="+mid)
    .then(()=> {
      btn.disabled = false;
      refreshLive(mid);
    });
</script>

<?php include("partials/layout_bottom.php"); ?>
