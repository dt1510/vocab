<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="jquery-2.0.2.min.js"></script>
</head>
<body>
<?php
include "common.php";

$con = db_connect();

$words = get_active_list($con);

foreach($words as $word) {
    word_heading($word);
    define_word($word);
}

?>

</body>
</html>
<style>
    div {padding-bottom: 1px;}
    keyword {color: #ff0000; font-weight: bold;}
    div.entry {padding-bottom:1px;}
    div.wn {color: #777;}
</style>
