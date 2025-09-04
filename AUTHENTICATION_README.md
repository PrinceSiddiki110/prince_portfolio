# Enhanced Authentication System

This portfolio website now includes a comprehensive, secure authentication system with session and cookie management.

## 🚀 Features

### **Session Management**
- **Secure Sessions**: HTTP-only, secure cookies with SameSite protection
- **Session Timeout**: 8-hour maximum session duration
- **Session Regeneration**: Automatic session ID rotation every 30 minutes
- **Session Fixation Protection**: IP address validation
- **User Agent Validation**: Prevents session hijacking

### **Cookie Management**
- **Remember Me**: 30-day persistent login option
- **Secure Cookies**: HTTPS-only when available
- **HttpOnly**: Prevents XSS attacks
- **SameSite**: CSRF protection

### **Security Features**
- **CSRF Protection**: Token-based request validation
- **Password Security**: Prepared statements prevent SQL injection
- **Rate Limiting**: Login attempt tracking (database table provided)
- **Activity Logging**: Admin action tracking
- **Security Headers**: XSS, clickjacking, and MIME sniffing protection

## 📁 Files Structure

```
├── functions.php              # Core authentication functions
├── session_config.php         # Session configuration
├── login.php                  # Enhanced login page
├── logout.php                 # Logout handler
├── admin.php                  # Protected admin panel
├── auth_tables.sql            # Database tables for auth
└── AUTHENTICATION_README.md   # This file
```

## 🗄️ Database Setup

1. **Run the SQL file** to create necessary tables:
   ```sql
   mysql -u your_username -p your_database < auth_tables.sql
   ```

2. **Tables created**:
   - `remember_tokens` - For "Remember Me" functionality
   - `sessions` - Optional database session storage
   - `login_attempts` - Security tracking
   - `admin_activity_logs` - Activity monitoring

## 🔐 Usage

### **Login**
```php
// Include functions
require_once 'functions.php';

// Check if already logged in
if (isAdminLoggedIn()) {
    // Redirect to admin panel
    header('Location: admin.php');
    exit;
}

// Handle login form submission
if ($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);
    
    // Validate credentials and login
    if (validateCredentials($username, $password)) {
        loginAdmin($admin_id, $username, $remember_me);
        header('Location: admin.php');
        exit;
    }
}
```

### **Protect Pages**
```php
// At the top of any protected page
require_once 'functions.php';
require_admin(); // This will redirect to login if not authenticated
```

### **Logout**
```php
// Simple logout
logout(); // This function handles everything
```

### **Check Authentication Status**
```php
if (isAdminLoggedIn()) {
    $admin = getCurrentAdmin();
    echo "Welcome, " . $admin['username'];
}
```

## 🛡️ Security Features

### **Session Security**
- **Automatic Timeout**: Sessions expire after 8 hours
- **Idle Detection**: 30-minute idle timeout
- **IP Validation**: Session tied to IP address
- **User Agent Validation**: Prevents session hijacking

### **Cookie Security**
- **HttpOnly**: JavaScript cannot access cookies
- **Secure**: Only sent over HTTPS
- **SameSite**: Prevents CSRF attacks
- **Automatic Cleanup**: Expired cookies are removed

### **CSRF Protection**
- **Token Generation**: Unique token per session
- **Token Validation**: All forms include CSRF tokens
- **Automatic Rotation**: New token after login

## 🔧 Configuration

### **Session Settings** (`session_config.php`)
```php
// Session timeout (8 hours)
ini_set('session.gc_maxlifetime', 28800);

// Cookie settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_samesite', 'Strict');
```

### **Customization**
- **Session Timeout**: Modify `28800` in `functions.php`
- **Idle Timeout**: Change `1800` in `isSessionIdle()`
- **Remember Me Duration**: Update `30 * 24 * 60 * 60` in `loginAdmin()`

## 📱 Remember Me Functionality

The "Remember Me" feature creates a secure token stored in both:
1. **Database**: `remember_tokens` table
2. **Secure Cookie**: HttpOnly, secure, SameSite

**Security Notes**:
- Tokens are 64-character hexadecimal strings
- Tokens expire after 30 days
- Each token is tied to a specific admin user
- Tokens are automatically cleaned up on logout

## 🚨 Security Best Practices

### **Production Deployment**
1. **Change Default Password**: Update admin password in database
2. **Use HTTPS**: Enable SSL/TLS for secure cookie transmission
3. **Strong Passwords**: Implement password complexity requirements
4. **Rate Limiting**: Monitor login attempts table
5. **Regular Cleanup**: Run cleanup scripts for expired sessions

### **Monitoring**
- Check `login_attempts` table for suspicious activity
- Monitor `admin_activity_logs` for user actions
- Review server logs for security events

## 🔍 Troubleshooting

### **Common Issues**

1. **Session Not Persisting**
   - Check cookie settings in browser
   - Verify session configuration
   - Check server session storage permissions

2. **Login Redirect Loop**
   - Clear browser cookies
   - Check session configuration
   - Verify database connection

3. **CSRF Token Errors**
   - Refresh the page to get new token
   - Check session status
   - Verify form includes hidden CSRF field

### **Debug Mode**
Add this to see session information:
```php
// Add to any page for debugging
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
```

## 📞 Support

For issues or questions:
1. Check server error logs
2. Verify database table structure
3. Test with fresh browser session
4. Review security headers in browser dev tools

## 🔄 Updates

This authentication system is designed to be:
- **Maintainable**: Clear function structure
- **Extensible**: Easy to add new security features
- **Secure**: Industry-standard security practices
- **Compatible**: Works with existing portfolio code

---

**Note**: This system replaces the previous simple session-based authentication with a comprehensive, secure solution that follows modern web security best practices.
