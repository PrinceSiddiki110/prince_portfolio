<?php
session_start();
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
require_admin();

$skill = null;
$message = '';
$error = '';

// Get skill ID
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: manage_skills.php');
    exit;
}

// Fetch skill data
$stmt = $mysqli->prepare("SELECT * FROM skills WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$skill = $result->fetch_assoc();
$stmt->close();

if (!$skill) {
    header('Location: manage_skills.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $level = (int)($_POST['level'] ?? 0);
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    // Validate inputs
    if (empty($name) || empty($category)) {
        $error = "Name and category are required.";
    } elseif ($level < 0 || $level > 100) {
        $error = "Level must be between 0 and 100.";
    } else {
        $sql = "UPDATE skills SET name = ?, category = ?, level = ?, sort_order = ? WHERE id = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('ssiii', $name, $category, $level, $sort_order, $id);
            if ($stmt->execute()) {
                $message = "Skill updated successfully!";
                // Update local skill data
                $skill['name'] = $name;
                $skill['category'] = $category;
                $skill['level'] = $level;
                $skill['sort_order'] = $sort_order;
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database prepare error: " . $mysqli->error;
        }
    }
}

// Get available categories
$categories = [];
$res = $mysqli->query("SELECT DISTINCT category FROM skills WHERE category != '' ORDER BY category");
if ($res) {
    $categories = $res->fetch_all(MYSQLI_ASSOC);
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Skill - Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-panel">
    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
            <div class="dashboard-header">
                <h1>Edit Skill</h1>
                <div class="quick-actions">
                    <a href="manage_skills.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Back to Skills
                    </a>
                </div>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="name">Skill Name*</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($skill['name']); ?>"
                           placeholder="e.g., JavaScript, Python, React">
                </div>
                
                <div class="form-group">
                    <label for="category">Category*</label>
                    <select id="category" name="category" required>
                        <option value="">Select a category</option>
                        <option value="Programming" <?php echo $skill['category'] === 'Programming' ? 'selected' : ''; ?>>Programming</option>
                        <option value="Web Development" <?php echo $skill['category'] === 'Web Development' ? 'selected' : ''; ?>>Web Development</option>
                        <option value="Database" <?php echo $skill['category'] === 'Database' ? 'selected' : ''; ?>>Database</option>
                        <option value="Tools" <?php echo $skill['category'] === 'Tools' ? 'selected' : ''; ?>>Tools</option>
                        <option value="Machine Learning" <?php echo $skill['category'] === 'Machine Learning' ? 'selected' : ''; ?>>Machine Learning</option>
                        <option value="Design" <?php echo $skill['category'] === 'Design' ? 'selected' : ''; ?>>Design</option>
                        <option value="Other" <?php echo $skill['category'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="level">Skill Level (0-100)*</label>
                    <div class="level-input-group">
                        <input type="range" id="level" name="level" min="0" max="100" 
                               value="<?php echo $skill['level']; ?>" 
                               oninput="updateLevelDisplay(this.value)">
                        <div class="level-display">
                            <span id="levelValue"><?php echo $skill['level']; ?></span>%
                        </div>
                    </div>
                    <div class="level-labels">
                        <span>Beginner</span>
                        <span>Intermediate</span>
                        <span>Advanced</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" 
                           value="<?php echo $skill['sort_order']; ?>"
                           placeholder="Lower numbers appear first">
                    <small style="color: var(--a-text-soft); font-size: 0.75rem;">
                        Skills with lower sort order numbers appear first in the list
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn primary">
                        <i class="fas fa-save"></i> Update Skill
                    </button>
                    <a href="manage_skills.php" class="btn">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </main>
    </div>

    <script>
        function updateLevelDisplay(value) {
            document.getElementById('levelValue').textContent = value;
        }
    </script>
    <script src="js/admin.js"></script>
</body>
</html>
