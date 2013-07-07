<?php
include "common.php";

$con = db_connect();
update_0_level($con);
$level = level_count($con, 0);

$word = get_word($con);
echo "<h1>word: $word</h1>";
?>
