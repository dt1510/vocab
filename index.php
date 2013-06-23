<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="jquery-2.0.2.min.js"></script>
</head>
<body>
<?php
//load the revising group
$groups_total = file_get_contents(".groups_total")*1;
//current group marked from 1 to n
//$current_group = file_get_contents(".current_group")*1;
$current_group = current_group_from_timestamp($groups_total);

function current_group_from_timestamp($groups_total) {
    $date = new DateTime();
    $ts = $date->getTimestamp();    
    $days = $ts / (60*60*24);
    return ($days % $groups_total)+1;    
}

function group_number($entry, $groups_total) {
    $md5 = md5($entry);
    $places = 4;
    $head = substr($md5, 0, $places);
    $number = hexdec($head);
    $group_number = round($groups_total * ($number / pow(16, $places)));
    if($group_number == 0)
        $group_number = 1;
    if($group_number > $groups_total)
        $group_number = $groups_total;
    return $group_number;
}

//Learn English Vocabulary
function get_least_recent_word($current_group, $groups_total) {
//    `ls data --sort time -1 | head -1`
    $path = "./data"; 
    $latest_ctime = -1;
    $latest_filename = '';
    $d = dir($path);
    while (false !== ($entry = $d->read())) {
        $group_number = group_number($entry, $groups_total);
        if($group_number != $current_group)
            continue;  
      
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
$word = @$_GET["word"] == "" ? get_least_recent_word($current_group, $groups_total) : @$_GET["word"];
$pron = get_pronunciation($word);

echo "<h2 title='revising group $current_group'>$word <pron>$pron</pron></h2>";
echo "<script>";
echo "function archive() {";
echo "window.location = '/archive.php?word=$word';";
echo "}";
echo "</script>";

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
touch($old_file);
shell_exec("grep -h -C 1 $word corpus/* > .sentences");
$old_file = '.sentences';
$sentences = file($old_file, FILE_IGNORE_NEW_LINES);
foreach($sentences as $sentence) {
    $sentence = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $sentence);
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
        }
    });
    $('body').keydown(function(e){
        if(typing())
            return;
        //right arrow
        if (e.keyCode == 39 || e.keyCode == 40) { 
           window.location = "/";
        } else if (e.keyCode == 38) {
            $("#search").focus();
        }
        
        });
</script>
</html>
