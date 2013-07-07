<?php
$word = $_GET["word"];
//rename("data/$word.txt", "archive/$word.txt");
include "common.php";
$con = db_connect();
inc_level($con, $word);
header( 'Location: '.substr(dirname(__FILE__), strlen($_SERVER["DOCUMENT_ROOT"])) ) ;
?>
