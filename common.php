<?php
    include "config.php";

    function add_word($con, $word) {
        mysqli_query($con,"INSERT INTO vocabulary (word, level, revised)
        VALUES ('$word', -1, 0)");
    }

    //returns the list of words that in the process of being learnt
    function get_active_list($con) { 
        $result = mysqli_query($con, "SELECT word FROM vocabulary WHERE level>-1 ORDER BY revised ASC");
        $words = array();
        while($row = mysqli_fetch_array($result)) {
            array_push($words, $row[0]);
        }
        return $words;   
    }
    
    function inc_level($con, $word) {
        mysqli_query($con, "UPDATE vocabulary SET level=level+1 WHERE word='$word'");
    }
    
    function archive($con, $word) {
        mysqli_query($con, "UPDATE vocabulary SET level='".ARCHIVED_LEVEL."' WHERE word='$word'");
    }
    
    function update_timestamp($con, $word) {
        mysqli_query($con, "UPDATE vocabulary SET revised=CURRENT_TIMESTAMP WHERE word='$word'");
    }
    
    function level_count($con, $level) {
        $result = mysqli_query($con, "SELECT COUNT(*) as count FROM vocabulary WHERE level='$level'");
        $row = mysqli_fetch_array($result);
        return $row[0]*1;
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


    function word_heading($word) {
        $pron = get_pronunciation($word);
        echo "<h2>$word <pron>$pron</pron></h2>";
    }
    
    function get_regex($word) {
        return "\b(dis|in|mis)?$word(d|ed|s|es|r|er|ence|ment|ments|ly)?\b";
    }

    function highlight($word, $string) {
        return preg_replace("/(".get_regex($word).")/", "<keyword>$1</keyword>", $string);
        //return str_replace("$word", "<keyword>$word</keyword>", $string);
    }
    
    function define_word($word) {
        //wordnet definition
        shell_exec("wn $word -over > temp.txt");
        $wn_lines = file("temp.txt", FILE_IGNORE_NEW_LINES);
        echo "<div class='wn'>";
        echo "<div class='entry'>";
        foreach($wn_lines as $wn_line) {
            if($wn_line == "")
                echo "</div><div class='entry'>";    
            preg_match("/^[0-9]/", $wn_line, $matches);    
            if(count($matches) == 0)
                continue;
            echo highlight($word, $wn_line)."<br />";
        }
        echo "</div>";
        echo "</div>";
    }
    
    //if there are not sufficiently many words at the level 0, then words which have not been learnt yet, are added
    function update_0_level($con) {
        $level_count = level_count($con, 0);
        $needed = LEVEL_0_WORDS - $level_count;
        if($needed<0) {
            echo "Error more words at the level 0 than required.";
        } else if ($needed==0){
        } else {
            mysqli_query($con, "UPDATE vocabulary SET level=0 WHERE level='".NOT_LEARNING_LEVEL."' LIMIT $needed");
        }
    }
    
    function timer() {
        ?>
        <div id="timer" style="float:right; padding:2px;padding-right:20px;">0</div>
        <script>
            function increment_timer() {
                $('#timer').html(parseInt($('#timer').html(), 10)+1);
            }
            setInterval(increment_timer, 1000);
        </script>
        <?
    }
    
    function get_word($con) {
        $levels = array();
        for($i=0; $i<LEARNING_LEVELS; $i++) {
            $levels[$i]=level_count($con, $i);
        }

        //choose level
        $importance = array();
        for($i=0; $i<LEARNING_LEVELS; $i++) {
            $importance[$i] = $levels[$i]*pow(SPACED_REPETITION_COEFFICIENT, LEARNING_LEVELS-$i-1);
        }
        
        $chosen_level = 0;
        $rand = mt_rand(0, array_sum($importance)-1);
        $upto = 0;
        for($i=0; $i<LEARNING_LEVELS; $i++) {
            $upto+=$importance[$i];
            if($rand<$upto) {
                $chosen_level = $i;
                break;
            }
        }
        $result = mysqli_query($con, "SELECT word FROM vocabulary WHERE level='$chosen_level' ORDER BY revised ASC LIMIT 1");
        $row = mysqli_fetch_array($result);
        return $row[0];
    }
    
    function db_connect() {
        $con=mysqli_connect(DB_DOMAIN, DB_USERNAME, DB_PASSWORD, DB_NAME);
        // Check connection
        if (mysqli_connect_errno())
          {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
          }
       return $con;
    }
?>
