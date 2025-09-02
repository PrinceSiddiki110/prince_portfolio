<?php
require __DIR__ . '/db.php';

$username = 'admin';
$password = 'ChangeMe123!'; // choose a secure password

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
if ($stmt) {
	$stmt->bind_param('ss', $username, $hash);
	$stmt->execute();
	$stmt->close();
}

echo "Admin user created. Delete create_admin.php now.";
