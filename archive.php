<?php
$word = $_GET["word"];
rename("data/$word.txt", "archive/$word.txt");
header( 'Location: /' ) ;
?>