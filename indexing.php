<h2>Vocabulary Indexing</h2>
indexing...
<?php
$dir = "corpus/";
$texts = glob($dir."*.txt");

foreach($texts as $text)
{
    echo "<h4>$text</h4>";
    index_file();
    flush();
}
echo "done.";
?>

