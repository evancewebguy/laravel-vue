<?php

// 1. Create an array of 10 random numbers between 1 and 20
$numbers = [];
for ($i = 0; $i < 10; $i++) {
    $numbers[] = rand(1, 20);
}

// 2. Filter numbers below 10
$filtered = array_filter($numbers, function($num) {
    return $num < 10;
});

// 3. Output both arrays
echo "Original array: ";
print_r($numbers);

echo "\nFiltered array (numbers < 10): ";
print_r($filtered);
