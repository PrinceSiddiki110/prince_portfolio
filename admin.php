<?php
session_start();

// Database connection (use mysqli from db.php)
require_once __DIR__ . '/db.php';

// Simple CSRF helper
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
function check_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Route
$action = $_GET['action'] ?? 'dashboard';

if ($action === 'login') {
    // Login page + handler
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $mysqli->prepare('SELECT id, password FROM admins WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($admin_id, $admin_password);
        $admin = null;
        if ($stmt->fetch()) {
            $admin = ['id' => $admin_id, 'password' => $admin_password];
        }
        $stmt->close();

        if ($admin && ($password == $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            // regenerate session id for security
            session_regenerate_id(true);
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Invalid credentials';
        }
    }

    // Login form
    ?>
    <!doctype html>
    <html>
    <head><meta charset="utf-8"><title>Admin Login</title></head>
    <body>
      <h1>Admin Login</h1>
      <?php if (!empty($error)) : ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      <form method="post" action="admin.php?action=login">
        <label>
          Username:<br>
          <input type="text" name="username" required>
        </label><br><br>
        <label>
          Password:<br>
          <input type="password" name="password" required>
        </label><br><br>
        <button type="submit">Login</button>
      </form>
    </body>
    </html>
    <?php
    exit;
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    header('Location: admin.php?action=login');
    exit;
}

// Protect admin pages
require_once __DIR__ . '/functions.php';
require_admin();

// No sections CRUD needed - removed

// Fetch statistics for dashboard using mysqli
$stats = [];
$res = $mysqli->query('SELECT COUNT(*) AS c FROM projects');
$stats['projects'] = ($res && ($r = $res->fetch_assoc())) ? (int)$r['c'] : 0;
$res = $mysqli->query('SELECT COUNT(*) AS c FROM skills');
$stats['skills'] = ($res && ($r = $res->fetch_assoc())) ? (int)$r['c'] : 0;
$res = $mysqli->query('SELECT COUNT(*) AS c FROM contact_messages');
$stats['messages'] = ($res && ($r = $res->fetch_assoc())) ? (int)$r['c'] : 0;
$res = $mysqli->query('SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0');
$stats['unread_messages'] = ($res && ($r = $res->fetch_assoc())) ? (int)$r['c'] : 0;

// Fetch recent items for dashboard
$res = $mysqli->query('SELECT id, title, type, created_at FROM projects ORDER BY created_at DESC LIMIT 5');
$recent_projects = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$res = $mysqli->query('SELECT id, name, subject, created_at, is_read FROM contact_messages ORDER BY created_at DESC LIMIT 5');
$recent_messages = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
// Dashboard view
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-panel">
    <div class="admin-layout">
        <?php include 'admin_sidebar.php'; ?>
        
        <main class="admin-main">
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <div class="quick-actions">
                <a href="add_project.php" class="btn primary"><i class="fas fa-plus"></i> New Project</a>
                <a href="add_skill.php" class="btn"><i class="fas fa-plus"></i> New Skill</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-diagram-project"></i>
                <div class="stat-content">
                    <h3><?php echo $stats['projects']; ?></h3>
                    <p>Projects</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-code"></i>
                <div class="stat-content">
                    <h3><?php echo $stats['skills']; ?></h3>
                    <p>Skills</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-envelope"></i>
                <div class="stat-content">
                    <h3><?php echo $stats['messages']; ?></h3>
                    <p>Messages</p>
                    <?php if ($stats['unread_messages'] > 0): ?>
                    <span class="badge"><?php echo $stats['unread_messages']; ?> unread</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <section class="dashboard-section">
                <h2>Recent Projects</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_projects as $project): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['title']); ?></td>
                                <td><span class="tag"><?php echo htmlspecialchars($project['type']); ?></span></td>
                                <td><?php echo date('M j, Y', strtotime($project['created_at'])); ?></td>
                                <td>
                                    <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn small">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="manage_projects.php" class="btn link">View All Projects <i class="fas fa-arrow-right"></i></a>
            </section>

            <section class="dashboard-section">
                <h2>Recent Messages</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_messages as $message): ?>
                            <tr class="<?php echo $message['is_read'] ? '' : 'unread'; ?>">
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($message['created_at'])); ?></td>
                                <td>
                                    <?php if ($message['is_read']): ?>
                                        <span class="tag gray">Read</span>
                                    <?php else: ?>
                                        <span class="tag primary">New</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="messages.php" class="btn link">View All Messages <i class="fas fa-arrow-right"></i></a>
            </section>
        </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>