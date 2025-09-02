<?php
// admin/db.php
declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'portfolio');
define('DB_USER', 'root');    // XAMPP default
define('DB_PASS', '');        // XAMPP default is empty

// mysqli connection (object oriented)
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
  die('MySQL connection failed: ' . $mysqli->connect_error);
}

// set charset
$mysqli->set_charset('utf8mb4');
