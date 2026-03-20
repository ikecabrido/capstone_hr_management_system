# Cross-Device Login Troubleshooting Guide

## Problem
Getting error: `Server returned database connection failed: SQLSTATE[HY000][1045] Access denied for user '@'localhost`

This means the username is being sent as empty to the database.

## Root Causes & Solutions

### 1. **POST Data Not Being Received**
The form data isn't reaching the server properly.

**Solutions:**

#### A. Test Your Form Submission
1. Open your browser's **Developer Tools** (F12)
2. Go to **Network** tab
3. Clear the form and enter credentials again
4. Click Login
5. Look for the POST request to `login.php`
6. Click on it and check the **Request** tab under **Form Data**
7. Verify that `username` and `password` are being sent

#### B. Check PHP Configuration
Access your XAMPP Control Panel or SSH and verify:

```bash
# Check post_max_size and upload_max_filesize in php.ini
php -r "echo ini_get('post_max_size') . PHP_EOL; echo ini_get('upload_max_filesize') . PHP_EOL;"
```

They should both be at least 2M or larger:
```ini
post_max_size = 20M
upload_max_filesize = 20M
```

### 2. **Accessing from Different Device/IP**

When accessing from another device using an IP address (e.g., `192.168.x.x`):

#### A. Ensure MySQL Allows Remote Connections
By default, MySQL might only accept localhost connections.

1. **Check MySQL User Permissions:**
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Check current user
SELECT User, Host FROM mysql.user WHERE User='root';

-- If you see only 'localhost', you need to update:
-- Option 1: Allow all hosts (less secure)
ALTER USER 'root'@'%' IDENTIFIED BY '';
FLUSH PRIVILEGES;

-- Option 2: Allow specific IP (MORE SECURE)
CREATE USER 'root'@'192.168.x.x' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'192.168.x.x';
FLUSH PRIVILEGES;

-- Option 3: Use different host in application (RECOMMENDED)
```

#### B. Update Database Connection (RECOMMENDED)
Edit [auth/database.php](auth/database.php):

```php
// Instead of hardcoding 'localhost', use server IP
private $host = "192.168.x.x"; // Use your server IP
```

Or better yet, use an environment variable or config file:

Create **config.php** in the root:
```php
<?php
// config.php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', 'hr_management');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Then update [auth/database.php](auth/database.php) to use it:
```php
<?php
require_once dirname(__DIR__) . '/config.php';

class Database
{
    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    // ... rest of code
}
```

### 3. **Session/Cookie Issues**

Sessions might not persist across devices/IPs.

**Fix:**
Edit [auth/auth.php](auth/auth.php) to verify session handling:
```php
public function __construct()
{
    // Ensure session started before any operations
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $this->userModel = new User();
}
```

### 4. **Debug Your Specific Issue**

I've created a debug file for you. To use it:

1. Access: `http://your-server-ip/debug_login.php`
2. Try submitting the form
3. Check what data is being received
4. Check your Apache error log: `C:\xampp\apache\logs\error.log`

The debug file will show:
- POST data being received
- Your client IP
- Your server IP
- Any empty fields

### 5. **Common Environment Issues**

#### Windows Firewall
```powershell
# Check if port 3306 (MySQL) is open
netstat -an | findstr "3306"

# If not listening, MySQL might not be running
# In XAMPP: Start MySQL from Control Panel
```

#### Apache Configuration
Check `C:\xampp\apache\conf\httpd.conf`:
```apache
# Should allow from all
<Directory "C:/xampp/htdocs">
    Require all granted
</Directory>
```

---

## Quick Diagnostic Steps

Run these in order:

1. **Test database connection locally:**
   ```bash
   php -r "
   try {
       \$pdo = new PDO('mysql:host=localhost;dbname=hr_management', 'root', '');
       echo 'Local connection: OK' . PHP_EOL;
   } catch (Exception \$e) {
       echo 'Error: ' . \$e->getMessage() . PHP_EOL;
   }
   "
   ```

2. **Test with server IP:**
   ```bash
   php -r "
   try {
       \$pdo = new PDO('mysql:host=192.168.x.x;dbname=hr_management', 'root', '');
       echo 'Remote connection: OK' . PHP_EOL;
   } catch (Exception \$e) {
       echo 'Error: ' . \$e->getMessage() . PHP_EOL;
   }
   "
   ```

3. **Test form submission:**
   - Visit the debug page: `http://server-ip/debug_login.php`
   - Submit test credentials
   - Check if username/password appear in output

---

## After Fixing

Once login works from other devices:

1. **Delete the debug file**: `debug_login.php`
2. **Update config.php** with proper database host
3. **Consider adding logging** for failed login attempts
4. **Enable SSL/TLS** if accessing over internet (HTTPS required)

