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
    define_word($word);
}

?>

</body>
</html>
