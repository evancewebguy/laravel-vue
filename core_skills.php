<?php

function createRandArr() {
    # 1. Create an array of 10 random numbers between 1 and 20;
    $a = [];
    for ($x = 0; $x < 10; $x++) {
        $rand = rand(1,20);
        $a[] = $rand;
    }

    return $a;
}

# 2.Filter numbers below 10 (using either a loop or array_filter);
function filterArr() {
    $a = createRandArr();

    $arr = [];

    for ($i=0; $i < count($a); $i++) { 
        if ($a[$i] < 10) {
            $arr[] = array_filter($a);
        }
    }
    return $arr;
}

# 3.
// ...
