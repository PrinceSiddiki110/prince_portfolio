<?php
require __DIR__ . '/admin/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /portfolio/?contact=invalid');
  exit;
}

$fromEmail = filter_var($_POST['fromEmail'] ?? '', FILTER_VALIDATE_EMAIL);
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$fromEmail || $subject === '' || $message === '') {
  header('Location: /portfolio/?contact=missing');
  exit;
}

// Insert
$stmt = $pdo->prepare("INSERT INTO messages (from_email, subject, message) VALUES (:email, :subject, :msg)");
$stmt->execute([
  ':email' => $fromEmail,
  ':subject' => $subject,
  ':msg' => $message
]);

header('Location: /portfolio/?contact=sent');
exit;
