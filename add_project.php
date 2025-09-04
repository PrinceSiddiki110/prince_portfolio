<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
initSecureSession();
require_admin();

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $tech_tags = trim($_POST['tech_tags'] ?? '');
    $repo_url = trim($_POST['repo_url'] ?? '');
    $thumb_filename = null;

    // Validate inputs
    if (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } else {
        // Handle image upload
        if (!empty($_FILES['thumb']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'svg'];
            $ext = strtolower(pathinfo($_FILES['thumb']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                $error = "Invalid file type. Please upload JPG, PNG, or SVG.";
            } elseif ($_FILES['thumb']['size'] > 2*1024*1024) {
                $error = "File is too large. Maximum size is 2MB.";
            } else {
                $newName = time() . '_' . preg_replace('/[^a-z0-9._-]/i', '', $_FILES['thumb']['name']);
                $upload_dir = 'assets/img/projects/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $dest = $upload_dir . $newName;
                if (move_uploaded_file($_FILES['thumb']['tmp_name'], $dest)) {
                    $thumb_filename = $newName;
                } else {
                    $error = "Failed to upload image.";
                }
            }
        }
        
        if (empty($error)) {
            // Generate URL-friendly slug
            $slug = strtolower(preg_replace('/[^a-z0-9-]/', '-', $title));
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');

            $sql = "INSERT INTO projects (title, slug, description, type, tags, github_url, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $mysqli->prepare($sql)) {
                // bind parameters (all strings)
                $stmt->bind_param('sssssss', $title, $slug, $description, $type, $tech_tags, $repo_url, $thumb_filename);
                if ($stmt->execute()) {
                    $message = "Project added successfully!";
                    // Clear form data
                    $title = $description = $type = $tech_tags = $repo_url = '';
                } else {
                    $error = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Database prepare error: " . $mysqli->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project - Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-panel">
    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>

        <main class="admin-main">
        <h1>Add New Project</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="admin-form" onsubmit="return validateForm(this)">
            <div class="form-group">
                <label for="title">Project Title*</label>
                <input type="text" id="title" name="title" required 
                       value="<?php echo htmlspecialchars($title ?? ''); ?>"
                       data-error="Project title is required">
            </div>
            
            <div class="form-group">
                <label for="description">Description*</label>
                <textarea id="description" name="description" required rows="5"
                          data-error="Project description is required"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="type">Project Type</label>
                <input type="text" id="type" name="type"
                       value="<?php echo htmlspecialchars($type ?? ''); ?>"
                       placeholder="e.g., Web, Mobile, Desktop">
            </div>
            
            <div class="form-group">
                <label for="tech_tags">Technologies</label>
                <input type="text" id="tech_tags" name="tech_tags"
                       value="<?php echo htmlspecialchars($tech_tags ?? ''); ?>"
                       placeholder="e.g., PHP, MySQL, JavaScript">
            </div>
            
            <div class="form-group">
                <label for="thumb">Project Image</label>
                <input type="file" id="thumb" name="thumb" accept="image/*"
                       onchange="handleImageUpload(this)">
                <div class="image-preview"></div>
                <small class="hint">Max size: 2MB. Allowed: JPG, PNG, SVG</small>
            </div>
            
            <div class="form-group">
                <label for="repo_url">Repository URL</label>
                <input type="url" id="repo_url" name="repo_url"
                       value="<?php echo htmlspecialchars($repo_url ?? ''); ?>"
                       placeholder="https://github.com/username/project">
            </div>
            
                <div class="form-actions">
                    <button type="submit" class="btn primary">
                        <i class="fas fa-save"></i> Save Project
                    </button>
                    <a href="admin.php" class="btn">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
            </main>
        </div>
    
    <script src="js/admin.js"></script>
</body>
</html>
