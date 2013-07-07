<?php
include "config.php";
$con=mysqli_connect(DB_DOMAIN, DB_USERNAME, DB_PASSWORD);
//var_dump($con);

if (!$con) {
    die('Could not connect: ' . mysql_error());
}

// Make DB_NAME the current database
$db_selected = mysqli_select_db($con, DB_NAME);

if (!$db_selected) {
  // If we couldn't, then it either doesn't exist, or we can't see it.
  $sql = 'CREATE DATABASE '.DB_NAME;

  if (mysqli_query($con, $sql)) {
      echo "Database ".DB_NAME." created successfully\n";
  } else {
      echo 'Error creating database: ' . mysql_error() . "\n";
  }
}

// Create table
//$sql="CREATE TABLE Persons(FirstName CHAR(30),LastName CHAR(30),Age INT)";
$sql="CREATE  TABLE IF NOT EXISTS `".DB_NAME."`.`vocabulary` (
  `word` VARCHAR(45) NOT NULL ,
  `level` INT(11) NULL DEFAULT -1 ,
  `revised` TIMESTAMP NULL DEFAULT 0 ,
  PRIMARY KEY (`word`) ,
  UNIQUE INDEX `word_UNIQUE` (`word` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8";

// Execute query
if (mysqli_query($con,$sql))
  {
  ///echo "Table vocabulary created successfully";
  }
else
  {
  echo "Error creating table: " . mysqli_error($con);
  }

echo "<p>Vocab installed.</p>";
?>
