<?php
$word = $_GET["word"];
rename("data/$word.txt", "archive/$word.txt");
header( 'Location: '.substr(dirname(__FILE__), strlen($_SERVER["DOCUMENT_ROOT"])) ) ;
?>
