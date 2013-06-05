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
    
    
$word = get_least_recent_word();
echo "<h2>$word</h2>";
$old_file = "./data/".$word.".txt";
$sentences = file($old_file, FILE_IGNORE_NEW_LINES);
touch($old_file);
foreach($sentences as $sentence) {
    $sentence = str_replace("$word", "<b>$word</b>", $sentence);
    echo "$sentence<br />";
}
        
?>
