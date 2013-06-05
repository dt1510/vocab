<h2>Vocabulary Indexing</h2>
indexing...
<?php
function index_word($word) {
    $dir = "corpus/";
    $files = glob($dir."*.txt");
    $examples = array();
    foreach($files as $file)
    {
        //echo "<h4>$text</h4>";
        $text = file_get_contents($file);
        $re = '/# Split sentences on whitespace between them.
    (?<=                # Begin positive lookbehind.
      [.!?]             # Either an end of sentence punct,
    | [.!?][\'"]        # or end of sentence punct and quote.
    )                   # End positive lookbehind.
    (?<!                # Begin negative lookbehind.
      Mr\.              # Skip either "Mr."
    | Mrs\.             # or "Mrs.",
    | Ms\.              # or "Ms.",
    | Jr\.              # or "Jr.",
    | Dr\.              # or "Dr.",
    | Prof\.            # or "Prof.",
    | Sr\.              # or "Sr.",
                        # or... (you get the idea).
    )                   # End negative lookbehind.
    \s+                 # Split on whitespace between sentences.
    /ix';
        $sentences =  preg_split($re,$text);
        foreach($sentences as $sentence) {
            preg_match('/'.$word.'/', $sentence, $matches);
            if(count($matches) > 0) {
                echo $sentence;
            }
        }        
    }
   
}

$vocabulary_file = "vl.txt";

$words = file($vocabulary_file, FILE_IGNORE_NEW_LINES);
foreach($words as $word) {
    echo "<h4>$word</h4>";
    index_word($word);
    break;
    flush();
}
echo "done.";
?>

