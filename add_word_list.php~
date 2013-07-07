<?php
include "common.php";

$con=db_connect();
$vocabulary_file = "vl.txt";
$words = file($vocabulary_file, FILE_IGNORE_NEW_LINES);
foreach($words as $word) {
    echo "<h4>$word</h4>";
    add_word($con, $word);
    //break;
    flush();
}

?>
