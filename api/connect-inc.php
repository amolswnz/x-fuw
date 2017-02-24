<?php
  date_default_timezone_set('Pacific/Auckland');  // Set NZ TimeZone
  define("HOST","localhost");
  define("DATABASE","");
  define("USERNAME","");
  define("PASSWORD","");

  // Disable E_WARNING for session_start()
        // error_reporting(E_ALL ^ E_WARNING);
  // Check in production environment

  /* Creating database connection */
  try {
   $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USERNAME, PASSWORD);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
   $pdo->exec("SET NAMES 'utf8'");
   return $pdo;
  } catch(PDOException $e) {
   die("<div class='alert alert-danger'>ERROR : " . $e->getMessage() . "</div>");
  }

/* Helper function to generate placeholders */
function placeholders($dataArray) {
    $result = array();
    $count = sizeof($dataArray);
    for($x=0; $x < $count; $x++) {
        $result[] = "?";
    }
    return implode(",", $result);
}
