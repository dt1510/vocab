<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="jquery-2.0.2.min.js"></script>
</head>
<body>
<?php
include "common.php";

$con = db_connect();
update_0_level($con);

function get_regex($word) {
    return "\b(dis|in|mis)?$word(d|ed|s|es|r|er|ence|ment|ments|ly)?\b";
}

function highlight($word, $string) {
    return preg_replace("/(".get_regex($word).")/", "<keyword>$1</keyword>", $string);
    //return str_replace("$word", "<keyword>$word</keyword>", $string);
}

function get_pronunciation($word) {
    $pron = shell_exec("grep -i '\b$word\b' cmudict.0.7a.txt | head -1");
    $pron = substr($pron, strlen($word));
    return arpabet_to_ipa($pron);
}

function arpabet_to_ipa($arpabet) {
    $arpabet_conversion = file("Arpabet-to-IPA.txt", FILE_IGNORE_NEW_LINES);
    //var_dump($arpabet_conversion);
    $arpabet_hash = array();
    foreach($arpabet_conversion as $entry) {
        $cols = split(",", $entry);        
        $arpabet_hash[$cols[0]] = $cols[2];        
        
    }
    $phonems = split(" ", $arpabet);
    $ipa = "";
    foreach($phonems as $phonem) {
        $phonem = trim($phonem);        
        $phonem = str_replace(range(2,9),'1',$phonem);
        /*if(strpos($phonem, "1") !== false)
            $ipa .= "&#716;";
        if(strpos($phonem, "0") !== false)
            $ipa .= "&#712;";*/
        if(@$arpabet_hash[$phonem] != "") {
            $ipa .= $arpabet_hash[$phonem];
        } else {
            $p = str_replace(range(0,9),'', $phonem);            
            $ipa .= @$arpabet_hash[$p] == "" ? $p : $arpabet_hash[$p];
        }
    }
    return $ipa;
}

//get the word to learn/revise
//$word = @$_GET["word"] == "" ? get_least_recent_word($current_group, $groups_total) : @$_GET["word"];
$word = @$_GET["word"] == "" ? get_word($con) : @$_GET["word"];
update_timestamp($con, $word);
$pron = get_pronunciation($word);

?>
<div id="panel">
<form method="GET">
<input id="search" type="search" name="word">
</form>
</div>
<?

echo "<h2>$word <pron>$pron</pron></h2>";
echo "<script>";
echo "function archive() {";
echo "window.location = '".substr(dirname(__FILE__), strlen($_SERVER["DOCUMENT_ROOT"]))."/archive.php?word=$word';";
echo "}";
echo "function inc_level() {";
echo "window.location = '".substr(dirname(__FILE__), strlen($_SERVER["DOCUMENT_ROOT"]))."/inc_level.php?word=$word';";
echo "}";
echo "</script>";

define_word($word);

//sentential examples
echo "<div>";
shell_exec("grep -h -C 1 -E \"".get_regex($word)."\" corpus/* | head -100 > .sentences");
$sentences = file(".sentences", FILE_IGNORE_NEW_LINES);
$hash = array();
foreach($sentences as $sentence) {
    //do not display the duplicate entries unless the entry is an empty line
    $now_new_line = strlen($sentence)<5;
    $duplicate_line_break = $now_new_line && @$previous_new_line;
    $duplicate_sentence_entry = isset($hash[$sentence]) && !$now_new_line;
    //echo "dup$duplicate_line_break isset".isset($hash[$sentence])."<br />";
    if($duplicate_sentence_entry || $duplicate_line_break)
        continue;
    $previous_new_line = strlen($sentence)<5;
    //echo "<h1>$previous_not_new_line</h2>";
        
    $hash[$sentence]=1;
    
    $sentence = @iconv("UTF-8", "ISO-8859-1//TRANSLIT", $sentence);
    //$sentence = htmlentities($sentence);
    $sentence = highlight($word, $sentence);
    echo "$sentence<br />";
}
echo "</div>";
        
?>
</body>
<script>
    function typing() {
        return $("#search").is(":focus");
    }
    
    $('body').keypress(function() {
        if(typing())
            return;
        if(event.keyCode == 97) {
            archive();
        } else
        if(event.keyCode == 105) {
            inc_level();
        }
    });
    $('body').keydown(function(e){
        if(typing())
            return;
        //right arrow
        if (e.keyCode == 39 || e.keyCode == 40) { 
           window.location.replace(window.location.pathname);
        } else if (e.keyCode == 38) {
            $("#search").focus();
        }
        
        });
</script>
</html>
