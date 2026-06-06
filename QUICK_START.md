# 🚀 Quick Start Guide - How to Run

## Prerequisites

You need one of these local server environments:
- **XAMPP** (Recommended for Windows)
- **WAMP** (Windows)
- **MAMP** (Mac)
- **LAMP** (Linux)

---

## Option 1: Using XAMPP (Recommended)

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP (default location: `C:\xampp` on Windows)
3. Start XAMPP Control Panel

### Step 2: Start Services
1. Click **Start** next to **Apache**
2. Click **Start** next to **MySQL**
3. Wait for both to show green "Running" status

### Step 3: Copy Project Files
1. Copy the entire `saas_bms` folder
2. Paste it into: `C:\xampp\htdocs\`
3. Final path should be: `C:\xampp\htdocs\saas_bms\`

### Step 4: Create Database
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar
3. Database name: `saas_bms`
4. Collation: `utf8mb4_unicode_ci`
5. Click **Create**

### Step 5: Import Database
1. Click on `saas_bms` database in left sidebar
2. Click **Import** tab at the top
3. Click **Choose File**
4. Select `database.sql` from your `saas_bms` folder
5. Click **Go** at the bottom
6. Wait for "Import has been successfully finished" message

### Step 6: Configure Database Connection
1. Open `saas_bms/config/database.php` in a text editor
2. Verify these settings (default XAMPP settings):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'saas_bms');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Empty for XAMPP
   ```
3. Save the file

### Step 7: Configure Application URL
1. Open `saas_bms/config/config.php` in a text editor
2. Find this line:
   ```php
   define('APP_URL', 'http://localhost/saas_bms');
   ```
3. Make sure it matches your setup
4. Save the file

### Step 8: Access the Application
1. Open your web browser
2. Go to: `http://localhost/saas_bms`
3. You should see the login page

### Step 9: Login
Use these default credentials:
- **Username**: `admin`
- **Password**: `admin123`

### Step 10: Success! 🎉
You should now see the dashboard with:
- Statistics cards
- Charts
- Sample data
- Expiring services alerts

---

## Option 2: Using WAMP

### Steps:
1. Install WAMP from http://www.wampserver.com/
2. Start WAMP (icon should be green)
3. Copy `saas_bms` folder to `C:\wamp64\www\`
4. Go to `http://localhost/phpmyadmin`
5. Create database `saas_bms`
6. Import `database.sql`
7. Configure `config/database.php` (user: `root`, password: empty)
8. Access: `http://localhost/saas_bms`
9. Login: admin / admin123

---

## Option 3: Using Built-in PHP Server (Quick Test)

### For Quick Testing Only:

1. Open Command Prompt/Terminal
2. Navigate to project folder:
   ```bash
   cd C:\Users\YourName\Documents\saas_bms
   ```

3. Start PHP server:
   ```bash
   php -S localhost:8000
   ```

4. **Important**: You still need MySQL running!
   - Start XAMPP/WAMP MySQL service
   - Create database and import SQL

5. Access: `http://localhost:8000`

6. Update `config/config.php`:
   ```php
   define('APP_URL', 'http://localhost:8000');
   ```

---

## Troubleshooting

### Problem: "Database connection failed"
**Solution:**
- Make sure MySQL is running in XAMPP/WAMP
- Check database name is `saas_bms`
- Verify credentials in `config/database.php`
- Default XAMPP: user=`root`, password=empty

### Problem: "Page not found" or blank page
**Solution:**
- Check Apache is running
- Verify URL: `http://localhost/saas_bms` (not `saas_bms/index.php`)
- Check project is in correct folder (`htdocs` for XAMPP)

### Problem: "Table doesn't exist"
**Solution:**
- Database not imported correctly
- Go to phpMyAdmin
- Select `saas_bms` database
- Import `database.sql` again

### Problem: Login doesn't work
**Solution:**
- Check database has data
- In phpMyAdmin, run this query:
  ```sql
  SELECT * FROM users WHERE username = 'admin';
  ```
- If no result, import database again

### Problem: CSS not loading (page looks broken)
**Solution:**
- Check `APP_URL` in `config/config.php`
- Should match your actual URL
- Clear browser cache (Ctrl+F5)

---

## Default Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin123`

**⚠️ IMPORTANT**: Change this password after first login!

---

## What You'll See After Login

### Dashboard Features:
1. **Statistics Cards** (top row)
   - Total Clients: 3
   - Active Domains: 4
   - Active Hosting: 3
   - Monthly Revenue

2. **Charts** (middle section)
   - Monthly Revenue Bar Chart
   - Service Distribution Pie Chart

3. **Expiring Services** (bottom section)
   - Domains expiring in next 30 days
   - Hosting expiring in next 30 days
   - Color-coded alerts (red=urgent, yellow=warning)

4. **Recent Activity**
   - Recent Payments
   - Recent Notifications

### Sample Data Included:
- 3 Clients (John Doe, Jane Smith, Bob Johnson)
- 4 Domains (various providers)
- 3 Hosting accounts
- 5 Payment records

---

## Next Steps After Login

1. **Explore the Dashboard**
   - View statistics
   - Check expiring services
   - Review sample data

2. **Change Default Password**
   - Click on user avatar (top right)
   - Go to profile settings
   - Change password

3. **Review Sample Data**
   - Check clients, domains, hosting
   - Understand the data structure
   - Delete or modify as needed

4. **Start Adding Your Data**
   - Add your real clients
   - Record their domains
   - Track hosting subscriptions
   - Log payments

---

## File Locations Reference

### XAMPP:
- Project: `C:\xampp\htdocs\saas_bms\`
- Config: `C:\xampp\htdocs\saas_bms\config\`
- Database: Access via `http://localhost/phpmyadmin`

### WAMP:
- Project: `C:\wamp64\www\saas_bms\`
- Config: `C:\wamp64\www\saas_bms\config\`
- Database: Access via `http://localhost/phpmyadmin`

---

## Quick Command Reference

### Start XAMPP Services:
```bash
# Windows
C:\xampp\xampp-control.exe

# Or use XAMPP Control Panel GUI
```

### Access phpMyAdmin:
```
http://localhost/phpmyadmin
```

### Access Application:
```
http://localhost/saas_bms
```

### Import Database (Command Line):
```bash
cd C:\xampp\mysql\bin
mysql -u root -p saas_bms < C:\xampp\htdocs\saas_bms\database.sql
```

---

## Video Tutorial Steps (Summary)

1. ✅ Install XAMPP
2. ✅ Start Apache + MySQL
3. ✅ Copy project to htdocs
4. ✅ Create database in phpMyAdmin
5. ✅ Import database.sql
6. ✅ Open http://localhost/saas_bms
7. ✅ Login with admin/admin123
8. ✅ View dashboard
9. ✅ Explore features

---

## Support

If you encounter issues:
1. Check this guide's troubleshooting section
2. Review `INSTALLATION.md` for detailed setup
3. Verify all prerequisites are met
4. Check XAMPP/WAMP error logs

---

## System Requirements

- **PHP**: 7.4 or higher ✅
- **MySQL**: 5.7 or higher ✅
- **Web Server**: Apache (included in XAMPP/WAMP) ✅
- **Browser**: Chrome, Firefox, Edge, Safari ✅

---

## Success Checklist

- [ ] XAMPP/WAMP installed
- [ ] Apache running (green in control panel)
- [ ] MySQL running (green in control panel)
- [ ] Project copied to htdocs/www
- [ ] Database `saas_bms` created
- [ ] database.sql imported successfully
- [ ] Can access http://localhost/phpmyadmin
- [ ] Can access http://localhost/saas_bms
- [ ] Can see login page
- [ ] Can login with admin/admin123
- [ ] Dashboard loads with data

---

**You're all set! Enjoy managing your SaaS business! 🎉**