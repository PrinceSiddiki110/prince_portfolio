<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /portfolio/?contact=invalid');
  exit;
}

$name = trim($_POST['name'] ?? '');
$fromEmail = filter_var($_POST['fromEmail'] ?? '', FILTER_VALIDATE_EMAIL);
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$fromEmail || $subject === '' || $message === '') {
  header('Location: /portfolio/?contact=missing');
  exit;
}

// Insert into contact_messages table using mysqli
$stmt = $mysqli->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
if ($stmt) {
  $stmt->bind_param('ssss', $name, $fromEmail, $subject, $message);
  $stmt->execute();
  $stmt->close();
  
  // Redirect with success message
  header('Location: /portfolio/?contact=sent');
  exit;
} else {
  // Handle database error
  header('Location: /portfolio/?contact=error');
  exit;
}
