# SaaS Business Management System - Installation Guide

## 📋 Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache or Nginx
- **Extensions**: PDO, PDO_MySQL, mbstring, openssl

## 🚀 Installation Steps

### Step 1: Download and Extract

1. Download or clone this repository to your web server directory
2. Extract all files to your desired location (e.g., `htdocs/saas_bms` for XAMPP)

### Step 2: Database Setup

1. **Create Database**
   - Open phpMyAdmin or MySQL command line
   - Create a new database named `saas_bms`
   
   ```sql
   CREATE DATABASE saas_bms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Import Database Schema**
   - Locate the `database.sql` file in the root directory
   - Import it into your `saas_bms` database
   
   **Using phpMyAdmin:**
   - Select the `saas_bms` database
   - Click on "Import" tab
   - Choose the `database.sql` file
   - Click "Go"
   
   **Using MySQL Command Line:**
   ```bash
   mysql -u root -p saas_bms < database.sql
   ```

### Step 3: Configure Database Connection

1. Open `config/database.php`
2. Update the database credentials:

```php
define('DB_HOST', 'localhost');      // Your database host
define('DB_NAME', 'saas_bms');       // Your database name
define('DB_USER', 'root');           // Your database username
define('DB_PASS', '');               // Your database password
```

### Step 4: Configure Application Settings

1. Open `config/config.php`
2. Update the `APP_URL` constant to match your installation:

```php
define('APP_URL', 'http://localhost/saas_bms'); // Update this
```

**Examples:**
- Local XAMPP: `http://localhost/saas_bms`
- Local WAMP: `http://localhost/saas_bms`
- Production: `https://yourdomain.com`

### Step 5: Set Permissions (Linux/Mac)

If you're on Linux or Mac, set proper permissions:

```bash
chmod -R 755 /path/to/saas_bms
chmod -R 777 /path/to/saas_bms/assets
```

### Step 6: Access the Application

1. Open your web browser
2. Navigate to your installation URL (e.g., `http://localhost/saas_bms`)
3. You should see the login page

### Step 7: Login

Use the default admin credentials:

- **Username**: `admin`
- **Password**: `admin123`

**⚠️ IMPORTANT**: Change the default password immediately after first login!

## 🔧 Configuration Options

### Timezone

Update timezone in `config/config.php`:

```php
date_default_timezone_set('Asia/Calcutta'); // Change to your timezone
```

### Pagination

Change records per page in `config/config.php`:

```php
define('RECORDS_PER_PAGE', 10); // Change as needed
```

### Alert Days

Customize renewal alert thresholds in `config/config.php`:

```php
define('ALERT_DAYS_URGENT', 7);    // Red alert (7 days)
define('ALERT_DAYS_WARNING', 15);  // Yellow alert (15 days)
define('ALERT_DAYS_INFO', 30);     // Info alert (30 days)
```

### Currency

Change currency settings in `config/config.php`:

```php
define('CURRENCY_SYMBOL', '$');    // Change to your currency symbol
define('CURRENCY_CODE', 'USD');    // Change to your currency code
```

## 🌐 Web Server Configuration

### Apache (.htaccess)

Create a `.htaccess` file in the root directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /saas_bms/
    
    # Redirect to index.php if file doesn't exist
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?page=$1 [QSA,L]
</IfModule>

# Prevent directory listing
Options -Indexes

# Protect config files
<FilesMatch "^(database|config)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Nginx

Add this to your Nginx configuration:

```nginx
location /saas_bms {
    try_files $uri $uri/ /saas_bms/index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

## 📧 Email Notifications (Optional)

To enable email notifications for expiring services:

1. Update SMTP settings in `config/config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
```

2. Set up a cron job to run daily:

```bash
0 9 * * * php /path/to/saas_bms/cron/send_notifications.php
```

## 🔒 Security Recommendations

### Production Environment

1. **Disable Error Display**
   
   In `config/config.php`:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

2. **Change Default Password**
   - Login with default credentials
   - Go to user settings
   - Change password immediately

3. **Database User Permissions**
   - Create a dedicated MySQL user for the application
   - Grant only necessary permissions (SELECT, INSERT, UPDATE, DELETE)
   
   ```sql
   CREATE USER 'saas_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON saas_bms.* TO 'saas_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

4. **SSL Certificate**
   - Use HTTPS in production
   - Obtain SSL certificate (Let's Encrypt is free)

5. **File Permissions**
   - Set restrictive permissions on config files
   ```bash
   chmod 600 config/database.php
   chmod 600 config/config.php
   ```

## 🐛 Troubleshooting

### Database Connection Error

**Problem**: "Database connection failed"

**Solution**:
1. Verify database credentials in `config/database.php`
2. Ensure MySQL service is running
3. Check if database `saas_bms` exists
4. Verify user has proper permissions

### Blank Page or 500 Error

**Problem**: White screen or Internal Server Error

**Solution**:
1. Enable error reporting temporarily:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check PHP error logs
3. Verify all required PHP extensions are installed
4. Check file permissions

### Login Not Working

**Problem**: Cannot login with default credentials

**Solution**:
1. Verify database was imported correctly
2. Check if `users` table has data:
   ```sql
   SELECT * FROM users WHERE username = 'admin';
   ```
3. If no admin user, run this SQL:
   ```sql
   INSERT INTO users (username, email, password, full_name, role, status) 
   VALUES ('admin', 'admin@saas-bms.local', 
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
   'System Administrator', 'admin', 'active');
   ```

### CSS/JS Not Loading

**Problem**: Page appears without styling

**Solution**:
1. Verify `APP_URL` in `config/config.php` is correct
2. Check browser console for 404 errors
3. Ensure assets directory is accessible
4. Clear browser cache

### Session Issues

**Problem**: Keeps logging out or session errors

**Solution**:
1. Check PHP session configuration
2. Ensure session directory is writable
3. Verify `session.save_path` in php.ini
4. Check if cookies are enabled in browser

## 📊 Sample Data

The database includes sample data for testing:
- 3 sample clients
- 4 sample domains
- 3 sample hosting accounts
- 5 sample payments

You can delete this data after testing or keep it for reference.

## 🔄 Updates and Maintenance

### Backup Database

Regular backups are recommended:

```bash
mysqldump -u root -p saas_bms > backup_$(date +%Y%m%d).sql
```

### Update Application

1. Backup your database
2. Backup your `config` directory
3. Replace all files except `config` directory
4. Run any new database migrations if provided

## 📞 Support

For issues or questions:
- Check the troubleshooting section above
- Review PHP error logs
- Ensure all requirements are met

## 📝 Default Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin123`

**⚠️ Change this password immediately after first login!**

## ✅ Post-Installation Checklist

- [ ] Database imported successfully
- [ ] Database credentials configured
- [ ] APP_URL configured correctly
- [ ] Can access login page
- [ ] Can login with default credentials
- [ ] Default password changed
- [ ] Sample data reviewed/deleted
- [ ] Error reporting disabled (production)
- [ ] SSL certificate installed (production)
- [ ] Backup system configured
- [ ] File permissions set correctly

## 🎉 You're Ready!

Your SaaS Business Management System is now installed and ready to use!

Start by:
1. Adding your clients
2. Recording their domains and hosting
3. Tracking payments
4. Monitoring renewal dates

The dashboard will automatically show expiring services and generate notifications.