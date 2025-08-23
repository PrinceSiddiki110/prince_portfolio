<?php
// admin/db.php
declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'portfolio');
define('DB_USER', 'root');    // XAMPP default
define('DB_PASS', '');        // XAMPP default is empty

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    $options
  );
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
