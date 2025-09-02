<?php
session_start();
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
require_admin();

// Get skill ID
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: manage_skills.php');
    exit;
}

// Delete the skill
$stmt = $mysqli->prepare("DELETE FROM skills WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Skill deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete skill.";
}

$stmt->close();
header('Location: manage_skills.php');
exit;
