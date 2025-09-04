<?php
// Enhanced Authentication Functions with Session and Cookie Management

/**
 * Initialize secure session with proper configuration
 */
function initSecureSession() {
    // Configure session settings before starting (if not already started)
    if (session_status() === PHP_SESSION_NONE) {
        // Set session configuration
        @ini_set('session.cookie_httponly', 1);
        @ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        @ini_set('session.use_strict_mode', 1);
        @ini_set('session.cookie_samesite', 'Strict');
        @ini_set('session.gc_maxlifetime', 28800); // 8 hours
        @ini_set('session.cookie_lifetime', 0); // Session cookie (browser close)
        
        // Set session name
        @session_name('PORTFOLIO_ADMIN_SESSION');
        
        // Set session cache settings
        @session_cache_limiter('nocache');
        @session_cache_expire(0);
        
        // Start session
        session_start();
    }
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    // Validate session integrity
    if (!isset($_SESSION['session_created'])) {
        $_SESSION['session_created'] = time();
    }
    
    // Check for session fixation
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        // Log potential security issue
        error_log('Potential session fixation attempt: IP mismatch for session ' . session_id());
        logout();
    }
}

/**
 * Check if user is logged in with session validation
 */
function isAdminLoggedIn() {
    initSecureSession();
    
    // Check if session has admin data
    if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_logged_in_at'])) {
        return false;
    }
    
    // Check session timeout (8 hours)
    if (time() - $_SESSION['admin_logged_in_at'] > 28800) {
        logout();
        return false;
    }
    
    // Check if user agent matches (prevent session hijacking)
    if (!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        logout();
        return false;
    }
    
    // Check if IP address matches (optional, can be strict)
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        // Log potential security issue
        error_log('Potential session hijacking attempt: IP mismatch for admin ID ' . $_SESSION['admin_id']);
        logout();
        return false;
    }
    
    return true;
}

/**
 * Require admin authentication, redirect if not logged in
 */
function require_admin() {
    if (!isAdminLoggedIn()) {
        // Store intended URL for redirect after login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Login admin user with secure session creation
 */
function loginAdmin($admin_id, $username, $remember_me = false) {
    initSecureSession();
    
    // Clear any existing session data
    session_unset();
    
    // Set session data
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_logged_in_at'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['last_regeneration'] = time();
    
    // Generate new CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    
    // Set remember me cookie if requested
    if ($remember_me) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store remember me token in database (you'll need to add this table)
        // For now, we'll use a secure cookie
        setcookie('remember_token', $token, [
            'expires' => $expires,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        $_SESSION['remember_token'] = $token;
    }
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

/**
 * Logout admin user and clear all session/cookie data
 */
function logout() {
    initSecureSession();
    
    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
        unset($_COOKIE['remember_token']);
    }
    
    // Clear session
    session_unset();
    session_destroy();
    
    // Clear all cookies
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie, 2);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 3600, '/');
        }
    }
    
    // Redirect to login page
    header('Location: login.php');
    exit;
}

/**
 * Check remember me cookie and auto-login if valid
 */
function checkRememberMe() {
    if (isset($_COOKIE['remember_token']) && !isAdminLoggedIn()) {
        // Here you would validate the token against database
        // For now, we'll just check if it exists and is valid format
        $token = $_COOKIE['remember_token'];
        if (strlen($token) === 64 && ctype_xdigit($token)) {
            // Token format is valid, you should validate against database
            // For security, we'll require re-authentication
            return false;
        }
    }
    return false;
}

/**
 * Generate secure CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Clean up expired sessions (call this periodically)
 */
function cleanupExpiredSessions() {
    // This function can be called by a cron job or cleanup script
    $expired_time = time() - 28800; // 8 hours
    
    // You could implement database cleanup here for persistent sessions
    // For now, we'll just return the expired time
    return $expired_time;
}

/**
 * Get admin user info from session
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
        'logged_in_at' => $_SESSION['admin_logged_in_at']
    ];
}

/**
 * Update last activity timestamp
 */
function updateLastActivity() {
    if (isAdminLoggedIn()) {
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Check if session is idle and needs refresh
 */
function isSessionIdle() {
    if (!isAdminLoggedIn()) {
        return true;
    }
    
    $idle_timeout = 1800; // 30 minutes
    $last_activity = $_SESSION['last_activity'] ?? $_SESSION['admin_logged_in_at'];
    
    return (time() - $last_activity) > $idle_timeout;
}
?>
