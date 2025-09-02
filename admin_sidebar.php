<?php
// Check if admin is logged in
if (!function_exists('isAdminLoggedIn')) {
    require_once 'functions.php';
}
if (!isAdminLoggedIn()) {
    header('Location: adminlogin.php');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-header">
        <div class="admin-logo">
            <div class="logo-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="logo-text">
                <h2>Portfolio Admin</h2>
                <span>Management Panel</span>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="admin-nav">
        <ul>
            <li>
                <a href="admin.php" class="admin-nav-link <?php echo $current_page === 'admin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage_projects.php" class="admin-nav-link <?php echo $current_page === 'manage_projects.php' ? 'active' : ''; ?>">
                    <i class="fas fa-diagram-project"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li>
                <a href="add_project.php" class="admin-nav-link <?php echo $current_page === 'add_project.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Project</span>
                </a>
            </li>
            <li>
                <a href="manage_skills.php" class="admin-nav-link <?php echo $current_page === 'manage_skills.php' ? 'active' : ''; ?>">
                    <i class="fas fa-code"></i>
                    <span>Skills</span>
                </a>
            </li>
            <li>
                <a href="add_skill.php" class="admin-nav-link <?php echo $current_page === 'add_skill.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus"></i>
                    <span>Add Skill</span>
                </a>
            </li>
            <li>
                <a href="messages.php" class="admin-nav-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="admin-sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
                <span class="username">Admin</span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
        <a href="admin.php?action=logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle" id="mobileMenuToggle">
    <i class="fas fa-bars"></i>
</button>