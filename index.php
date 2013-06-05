<h2>Learn English Vocabulary</h2>
<?php
function get_most_recent_word() {
//    `ls data --sort time -1 | head -1`
    $path = "./data"; 

    $latest_ctime = 0;
    $latest_filename = '';    

    $d = dir($path);
    while (false !== ($entry = $d->read())) {
      $filepath = "{$path}/{$entry}";
      // could do also other checks than just checking whether the entry is a file
      if (is_file($filepath) && filectime($filepath) > $latest_ctime) {
        $latest_ctime = filectime($filepath);
        $latest_filename = $entry;
      }
    }
    $parts = preg_split("/[.]/", $latest_filename);
    return $parts[0];
}
    
    
$word = get_most_recent_word();
$recent_file = "./data/".$word.".txt";
$sentences = file($recent_file, FILE_IGNORE_NEW_LINES);
foreach($sentences as $sentence) {
    $sentence = str_replace("$word", "<b>$word</b>", $sentence);
    echo "$sentence<br />";
}
        
?>
