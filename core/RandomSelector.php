<?php
class RandomSelector {
    public static function choose($options){
        $rand = rand(1,100);
        $current = 0;
        foreach($options as $key=>$weight){
            $current += $weight;
            if($rand <= $current){
                return $key;
            }
        }
        return array_key_first($options);
    }
}
