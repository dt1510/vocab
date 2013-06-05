<h2>Vocabulary Indexing</h2>
indexing...
<?php
set_time_limit(0);
function index_word($word) {
    $word_file = "data/$word.txt";
    if(file_exists($word_file))
        return;
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
            $sentence = trim(preg_replace('/\s\s+/', ' ', $sentence));
            preg_match('/'.$word.'/', $sentence, $matches);
            if(count($matches) > 0) {
                array_push($examples, $sentence);
                if(count($examples) > 20)
                    goto ready;
            }
        }
    }
    
    ready:
    file_put_contents($word_file, join("\n", $examples));
}

$vocabulary_file = "vl.txt";

$words = file($vocabulary_file, FILE_IGNORE_NEW_LINES);
foreach($words as $word) {
    echo "<h4>$word</h4>";
    index_word($word);
    //break;
    flush();
}
echo "done.";
?>

