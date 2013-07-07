<?php
    include "config.php";

    function add_word($con, $word) {
        mysqli_query($con,"INSERT INTO vocabulary (word, level, revised)
        VALUES ('$word', -1, 0)");
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
