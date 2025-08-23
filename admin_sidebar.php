<?php
// Check if admin is logged in
if (!function_exists('isAdminLoggedIn')) {
    require_once 'functions.php';
}
if (!isAdminLoggedIn()) {
    header('Location: adminlogin.php');
    exit();
}
?>
<aside class="admin-sidebar">
    <div class="admin-logo">
        <h2>Admin Panel</h2>
        <button id="mobileMenu" class="mobile-menu-btn" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <nav class="admin-nav">
        <ul>
            <li>
                <a href="admin.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="add_project.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'add_project.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i> Add Project
                </a>
            </li>
            <li>
                <a href="manage_skills.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'manage_skills.php' ? 'active' : ''; ?>">
                    <i class="fas fa-code"></i> Manage Skills
                </a>
            </li>
            <li>
                <a href="messages.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Messages
                </a>
            </li>
            <li>
                <a href="settings.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
            <li>
                <a href="logout.php" class="admin-nav-link logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>
