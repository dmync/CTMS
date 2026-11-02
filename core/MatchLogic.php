<?php
include_once "RandomSelector.php";

class MatchLogic {

    public static function generateBall(){
        $event = RandomSelector::choose([
            "dot"=>50,
            "1"=>20,
            "2"=>10,
            "4"=>12,
            "6"=>2,
            "wicket"=>6
        ]);

        return $event;
    }
}
