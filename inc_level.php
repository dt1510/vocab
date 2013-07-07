<?php
//when a word is known then it can be moved to the next level
$word = $_GET["word"];
include "common.php";
$con = db_connect();
inc_level($con, $word);
header( 'Location: '.substr(dirname(__FILE__), strlen($_SERVER["DOCUMENT_ROOT"])) ) ;

?>
