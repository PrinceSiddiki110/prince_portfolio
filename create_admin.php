<?php
require __DIR__ . '/db.php';

$username = 'admin';
$password = 'ChangeMe123!'; // choose a secure password

$hash = password($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->execute([$username, $hash]);

echo "Admin user created. Delete create_admin.php now.";
