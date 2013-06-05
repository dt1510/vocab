<h2>Vocabulary Indexing</h2>
indexing...
<?php
set_time_limit(0);

function sentence_sort($a, $b) {
    return strlen($a)-strlen($b);
}

function index_word($word) {
    $word_file = "data/$word.txt";
    if(file_exists($word_file))
        return;
    $dir = "corpus/";
    $files = glob($dir."*.txt");
    $examples = array();
    foreach($files as $file)
    {
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
            $sentence = str_replace("\r\n", ' ', $sentence);
            $sentence = str_replace("\n", ' ', $sentence);
            preg_match('/\b'.$word.'\b/', $sentence, $matches);
            if(count($matches) > 0) {
                array_push($examples, $sentence);
                if(count($examples) > 50)
                    goto ready;
            }
        }
    }
    
    ready:
    usort($examples,'sentence_sort');
    file_put_contents($word_file, join("\n", array_slice($examples, 0, 20)));
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

