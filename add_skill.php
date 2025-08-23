<?php
require __DIR__ . '/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $category = trim($_POST['category']);
  $name = trim($_POST['name']);
  $level = (int)$_POST['level'];
  $stmt = $pdo->prepare("INSERT INTO skills (category, name, level) VALUES (?, ?, ?)");
  $stmt->execute([$category, $name, $level]);
  header('Location: manage_skills.php');
  exit;
}
?>
<form method="post">
  <input name="category" placeholder="Category" required><br>
  <input name="name" placeholder="Skill name" required><br>
  <input name="level" type="number" min="0" max="100" value="50" required> %<br>
  <button>Add</button>
</form>
