<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
initSecureSession();
require_admin();

// ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// Handle actions via GET (delete/mark-read)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_GET['action'] === 'delete') {
        $csrf = $_GET['csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
            header('HTTP/1.1 400 Bad Request');
            exit('Invalid CSRF token');
        }
        $stmt = $mysqli->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        header('Location: messages.php');
        exit;
    }

    if ($_GET['action'] === 'mark-read') {
        $stmt = $mysqli->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        header('Location: messages.php');
        exit;
    }
}

// Fetch messages
$messages = [];
$res = $mysqli->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
if ($res) {
    $messages = $res->fetch_all(MYSQLI_ASSOC);
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Portfolio Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-panel">
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="admin-layout">
        <main class="admin-main">
        <div class="dashboard-header">
            <h1>Messages</h1>
            <div class="quick-actions">
                <a href="export_messages.php" class="btn primary">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        <section class="dashboard-section">
            <div class="table-responsive">
                <table class="admin-table" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                        <tr class="<?php echo $message['is_read'] ? '' : 'unread'; ?>">
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="text-link">
                                    <?php echo htmlspecialchars($message['email']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                            <td>
                                <div class="message-preview">
                                    <?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?>
                                </div>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></td>
                            <td>
                                <?php if ($message['is_read']): ?>
                                    <span class="tag gray">Read</span>
                                <?php else: ?>
                                    <span class="tag primary">New</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <?php if (!$message['is_read']): ?>
                                    <a href="messages.php?action=mark-read&id=<?php echo $message['id']; ?>" 
                                       class="btn small"
                                       title="Mark as Read">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="messages.php?action=delete&id=<?php echo $message['id']; ?>&csrf=<?php echo $_SESSION['csrf_token']; ?>" 
                                       class="btn small danger"
                                       onclick="return confirm('Are you sure you want to delete this message?')"
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
