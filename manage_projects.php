<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
initSecureSession();
require_admin();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $message = "Project deleted successfully!";
    } else {
        $error = "Failed to delete project.";
    }
    $stmt->close();
}

// Fetch projects
$projects = [];
$res = $mysqli->query("SELECT * FROM projects ORDER BY created_at DESC");
if ($res) {
    $projects = $res->fetch_all(MYSQLI_ASSOC);
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Portfolio Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-panel">
    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="dashboard-header">
                <h1>Manage Projects</h1>
                <div class="quick-actions">
                    <a href="add_project.php" class="btn primary">
                        <i class="fas fa-plus"></i> New Project
                    </a>
                    <a href="admin.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <section class="dashboard-section">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Tags</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($project['image'])): ?>
                                        <img src="assets/img/projects/<?php echo htmlspecialchars($project['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($project['title']); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: #94a3b8;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: var(--a-text);">
                                        <?php echo htmlspecialchars($project['title']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--a-text-soft); margin-top: 0.25rem;">
                                        <?php echo htmlspecialchars(substr($project['description'], 0, 60)) . (strlen($project['description']) > 60 ? '...' : ''); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="tag"><?php echo htmlspecialchars($project['type']); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $tags = explode(',', $project['tags'] ?? '');
                                    foreach (array_slice($tags, 0, 3) as $tag):
                                        if (trim($tag)):
                                    ?>
                                        <span class="tag" style="font-size: 0.65rem; margin-right: 0.25rem;">
                                            <?php echo htmlspecialchars(trim($tag)); ?>
                                        </span>
                                    <?php
                                        endif;
                                    endforeach;
                                    if (count($tags) > 3):
                                    ?>
                                        <span style="font-size: 0.65rem; color: var(--a-text-soft);">+<?php echo count($tags) - 3; ?> more</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($project['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn small">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="manage_projects.php?action=delete&id=<?php echo $project['id']; ?>" 
                                           class="btn small danger"
                                           onclick="return confirm('Are you sure you want to delete this project?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($projects)): ?>
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 2rem; color: var(--a-text-soft);">
                                    <i class="fas fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                    No projects found. <a href="add_project.php">Add your first project</a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
