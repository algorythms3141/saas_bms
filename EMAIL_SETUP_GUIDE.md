# Email Notification Setup Guide

This guide explains how to set up automated email notifications for domain and hosting expiry alerts.

## Overview

The system will automatically send email notifications to `algorythms3141@gmail.com` **10 days before** any domain or hosting service expires.

## Files Created

1. **models/EmailHelper.php** - Email sending class with HTML templates
2. **cron_send_expiry_emails.php** - Cron job script for automated emails
3. **email_logs_table.sql** - Database table to track sent emails

## Setup Instructions

### Step 1: Create Email Logs Table

Run the SQL file to create the email logs table:

```bash
mysql -u root -p saas_bms < email_logs_table.sql
```

Or manually execute the SQL in phpMyAdmin.

### Step 2: Configure Email Settings (Optional)

The system uses PHP's built-in `mail()` function. If you want to customize the sender email, edit `config/config.php`:

```php
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'SaaS BMS Notifications');
```

### Step 3: Test Email Sending Manually

You can test the email system by running the cron script manually:

```bash
cd /path/to/saas_bms
php cron_send_expiry_emails.php
```

This will check for services expiring in exactly 10 days and send emails.

### Step 4: Set Up Cron Job (Automated Daily Emails)

#### On Linux/Mac:

1. Open crontab editor:
```bash
crontab -e
```

2. Add this line to run daily at 9:00 AM:
```bash
0 9 * * * /usr/bin/php /path/to/saas_bms/cron_send_expiry_emails.php >> /path/to/saas_bms/cron_email.log 2>&1
```

Replace `/path/to/saas_bms` with your actual path.

#### On Windows (Task Scheduler):

1. Open Task Scheduler
2. Create Basic Task
3. Name: "SaaS BMS Email Notifications"
4. Trigger: Daily at 9:00 AM
5. Action: Start a program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\saas_bms\cron_send_expiry_emails.php`
6. Save the task

#### On cPanel (Shared Hosting):

1. Go to cPanel → Cron Jobs
2. Add new cron job:
   - Minute: 0
   - Hour: 9
   - Day: *
   - Month: *
   - Weekday: *
   - Command: `/usr/bin/php /home/username/public_html/saas_bms/cron_send_expiry_emails.php`

## How It Works

### Email Trigger Logic

The system sends emails when:
- A domain's expiry date is **exactly 10 days from today**
- A hosting service's expiry date is **exactly 10 days from today**
- The service status is **'active'**

### Email Content

Each email includes:
- ⚠️ Alert header with urgency indicator
- Complete service details (domain/hosting info)
- Client information
- Days left until expiry
- Auto-renew status
- Cost information
- Direct link to edit the service in the system
- Action items checklist

### Email Tracking

All sent emails are logged in the `email_logs` table with:
- Recipient email
- Subject
- Status (sent/failed)
- Timestamp

## Testing the System

### Test with Existing Data

1. Find a domain or hosting in your database
2. Update its expiry date to 10 days from today:

```sql
-- For testing domain emails
UPDATE domains 
SET expiry_date = DATE_ADD(CURDATE(), INTERVAL 10 DAY) 
WHERE id = 1;

-- For testing hosting emails
UPDATE hosting 
SET expiry_date = DATE_ADD(CURDATE(), INTERVAL 10 DAY) 
WHERE id = 1;
```

3. Run the cron script manually:
```bash
php cron_send_expiry_emails.php
```

4. Check `algorythms3141@gmail.com` for the email

### View Email Logs

Check sent emails in the database:

```sql
SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 10;
```

## Customization

### Change Email Recipient

Edit `models/EmailHelper.php` and change the email address in these methods:
- Line 44: `sendDomainExpiryNotification()`
- Line 54: `sendHostingExpiryNotification()`

```php
$to = 'your-email@example.com'; // Change this
```

### Change Alert Days

To send emails at different intervals (e.g., 7 days, 15 days), edit `cron_send_expiry_emails.php`:

```php
// Change INTERVAL 10 DAY to your preferred number
WHERE d.expiry_date = DATE_ADD(CURDATE(), INTERVAL 7 DAY)
```

### Multiple Alert Intervals

To send emails at multiple intervals (10 days, 7 days, 3 days), modify the WHERE clause:

```php
WHERE d.expiry_date IN (
    DATE_ADD(CURDATE(), INTERVAL 10 DAY),
    DATE_ADD(CURDATE(), INTERVAL 7 DAY),
    DATE_ADD(CURDATE(), INTERVAL 3 DAY)
)
```

### Customize Email Templates

Edit the HTML templates in `models/EmailHelper.php`:
- `getDomainExpiryEmailTemplate()` - Domain email template
- `getHostingExpiryEmailTemplate()` - Hosting email template

## Troubleshooting

### Emails Not Sending

1. **Check PHP mail() function:**
```bash
php -r "mail('test@example.com', 'Test', 'Test message');"
```

2. **Check server mail logs:**
```bash
tail -f /var/log/mail.log
```

3. **Verify cron is running:**
```bash
grep CRON /var/log/syslog
```

4. **Check email_logs table:**
```sql
SELECT * FROM email_logs WHERE status = 'failed';
```

### Emails Going to Spam

1. Set up SPF records for your domain
2. Set up DKIM authentication
3. Use a proper FROM email address (not noreply@localhost)
4. Consider using SMTP instead of mail() function

### Cron Not Running

1. Check cron service status:
```bash
sudo service cron status
```

2. Check cron logs:
```bash
grep cron /var/log/syslog
```

3. Verify file permissions:
```bash
chmod +x cron_send_expiry_emails.php
```

## Advanced: Using SMTP

For better email delivery, you can modify `EmailHelper.php` to use PHPMailer with SMTP:

1. Install PHPMailer:
```bash
composer require phpmailer/phpmailer
```

2. Update the `send()` method to use SMTP instead of mail()

## Support

For issues or questions, check:
- Email logs: `SELECT * FROM email_logs`
- Cron logs: Check the log file specified in cron command
- PHP error logs: `/var/log/php_errors.log`

---

**Made with Bob**