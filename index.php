<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<?php
//Learn English Vocabulary
function get_least_recent_word() {
//    `ls data --sort time -1 | head -1`
    $path = "./data"; 
    $latest_ctime = -1;
    $latest_filename = '';
    $d = dir($path);
    while (false !== ($entry = $d->read())) {
      $filepath = "{$path}/{$entry}";
      // could do also other checks than just checking whether the entry is a file
      if (is_file($filepath) && ($latest_ctime == -1 || filectime($filepath) < $latest_ctime)) {
        $latest_ctime = filectime($filepath);
        $latest_filename = $entry;
      }
    }
    $parts = preg_split("/[.]/", $latest_filename);
    return $parts[0];
}

function highlight($word, $string) {
    return str_replace("$word", "<keyword>$word</keyword>", $string);
}

?>
<div id="search">
<form method="GET">
<input type="search" name="word">
</form>
</div>
<?

//get the word to learn/revise
$word = $_GET["word"] == "" ? get_least_recent_word() : $_GET["word"];
echo "<h2>$word</h2>";
//wordnet definition
shell_exec("wn $word -over > temp.txt");
$wn_lines = file("temp.txt", FILE_IGNORE_NEW_LINES);
echo "<div class='wn'>";
echo "<div class='entry'>";
foreach($wn_lines as $wn_line) {
    if($wn_line == "")
        echo "</div><div class='entry'>";
        
    //syns{n|v|a|r}
    /*preg_match("/^The noun/", $wn_line, $matches);
    if(count($matches) > 0) {
        shell_exec("wn $word -synsn > temp.txt");
        $wn_syn_lines = file("temp.txt", FILE_IGNORE_NEW_LINES);
        print_r($wn_syn_lines);
    }*/
    
    preg_match("/^[0-9]/", $wn_line, $matches);    
    if(count($matches) == 0)
        continue;
    echo highlight($word, $wn_line)."<br />";
}
echo "</div>";
echo "</div>";

//sentential examples
echo "<div>";
$old_file = "./data/".$word.".txt";
$sentences = file($old_file, FILE_IGNORE_NEW_LINES);
touch($old_file);
foreach($sentences as $sentence) {
    $sentence = highlight($word, $sentence);
    echo "$sentence<br />";
}
echo "</div>";
        
?>
