# 🚀 SaaS Business Management System

A comprehensive PHP-based internal web application designed to manage client websites, domains, hosting subscriptions, and payments. This is not a simple CRUD application but a scalable mini-SaaS system with advanced features.

![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## ✨ Features

### 🔐 Authentication System
- Secure login with session management
- Role-based access control (Admin/Staff)
- Password hashing with bcrypt
- Activity logging

### 👥 Client Management
- Add, edit, and delete clients
- Store comprehensive client information
- Track client statistics
- Search and filter capabilities
- View client history and services

### 🌐 Domain Management
- Track domains from multiple providers (GoDaddy, Namecheap, etc.)
- Monitor expiry dates with color-coded alerts
- Automatic status updates (active/expired)
- Calculate days remaining
- Link domains to clients
- Search and filter by provider, status, or client

### 🖥️ Hosting Management
- Manage hosting subscriptions
- Track server details and plans
- Monitor renewal dates
- Link hosting to domains and clients
- Automatic expiry detection
- Disk space and bandwidth tracking

### 💳 Payment Tracking
- Record all payments (domains, hosting, other)
- Multiple payment methods support
- Payment status tracking (paid, pending, failed, refunded)
- Transaction ID recording
- Revenue analytics
- Monthly revenue reports

### 📊 Advanced Dashboard
- Real-time statistics cards
- Interactive charts (Chart.js)
  - Monthly revenue bar chart
  - Service distribution pie chart
- Expiring services overview
- Recent payments list
- Recent notifications feed
- Color-coded alerts (urgent, warning, info)

### 🔔 Notification System
- Automatic notifications for expiring services
- Priority-based alerts (urgent, high, medium, low)
- 30-day, 15-day, and 7-day warnings
- Unread notification counter
- Notification history

### 🔍 Search & Filter
- Global search across all modules
- Advanced filtering options
- Date range filters
- Status-based filtering
- Provider/server filtering

### 📄 Pagination
- Configurable records per page
- Smooth navigation
- Performance optimized for large datasets

### 🎨 Modern UI/UX
- Responsive Bootstrap 5 design
- Mobile-friendly interface
- Gradient color schemes
- Interactive hover effects
- Icon-rich interface (Bootstrap Icons)
- Clean and professional layout

### 🔒 Security Features
- SQL injection prevention (PDO prepared statements)
- XSS protection (input sanitization)
- CSRF token protection
- Password hashing (bcrypt)
- Session security
- Activity logging for audit trail

## 📁 Project Structure

```
saas_bms/
├── config/
│   ├── config.php          # Main configuration
│   └── database.php        # Database connection
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── ClientController.php
│   ├── DomainController.php
│   ├── HostingController.php
│   ├── PaymentController.php
│   └── NotificationController.php
├── models/
│   ├── Model.php           # Base model class
│   ├── User.php
│   ├── Client.php
│   ├── Domain.php
│   ├── Hosting.php
│   ├── Payment.php
│   └── Notification.php
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── auth/
│   │   └── login.php
│   ├── dashboard/
│   │   └── index.php
│   ├── clients/
│   ├── domains/
│   ├── hosting/
│   ├── payments/
│   └── notifications/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── public/
├── index.php               # Main entry point
├── database.sql            # Database schema
├── INSTALLATION.md         # Installation guide
└── README.md              # This file
```

## 🛠️ Technology Stack

- **Backend**: Core PHP 7.4+ (MVC Architecture)
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: HTML5, CSS3, Bootstrap 5.3
- **JavaScript**: Vanilla JS, Chart.js
- **Icons**: Bootstrap Icons
- **Architecture**: MVC Pattern
- **Database Access**: PDO with prepared statements

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- Apache or Nginx web server
- PHP Extensions: PDO, PDO_MySQL, mbstring, openssl

## 🚀 Quick Start

### 1. Clone or Download

```bash
git clone https://github.com/yourusername/saas_bms.git
cd saas_bms
```

### 2. Create Database

```sql
CREATE DATABASE saas_bms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Import Database

```bash
mysql -u root -p saas_bms < database.sql
```

### 4. Configure

Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'saas_bms');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Edit `config/config.php`:
```php
define('APP_URL', 'http://localhost/saas_bms');
```

### 5. Access Application

Open browser: `http://localhost/saas_bms`

**Default Login:**
- Username: `admin`
- Password: `admin123`

**⚠️ Change password immediately after first login!**

## 📖 Detailed Installation

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md)

## 🗃️ Database Schema

The system uses a normalized database structure with the following main tables:

- **users** - System users (admin/staff)
- **clients** - Client information
- **domains** - Domain registrations
- **hosting** - Hosting subscriptions
- **payments** - Payment records
- **notifications** - System notifications
- **activity_logs** - Audit trail

### Relationships:
- Clients → Domains (One-to-Many)
- Clients → Hosting (One-to-Many)
- Clients → Payments (One-to-Many)
- Domains → Hosting (One-to-One, optional)
- Users → Activity Logs (One-to-Many)

## 🎯 Key Features Explained

### Automatic Status Updates

The system automatically:
- Updates expired domains and hosting
- Generates renewal notifications
- Calculates days remaining
- Color-codes alerts based on urgency

### Smart Notifications

Notifications are generated for:
- Domains expiring in 30, 15, and 7 days
- Hosting expiring in 30, 15, and 7 days
- Priority levels: urgent (7 days), high (15 days), medium (30 days)

### Revenue Analytics

Track revenue with:
- Monthly revenue charts
- Service-wise distribution
- Payment status tracking
- Pending payments overview

### Search Capabilities

Search across:
- Client names, emails, companies
- Domain names, providers
- Server names, hosting plans
- Payment transactions

## 🔧 Configuration Options

### Timezone
```php
date_default_timezone_set('Asia/Calcutta');
```

### Pagination
```php
define('RECORDS_PER_PAGE', 10);
```

### Alert Thresholds
```php
define('ALERT_DAYS_URGENT', 7);
define('ALERT_DAYS_WARNING', 15);
define('ALERT_DAYS_INFO', 30);
```

### Currency
```php
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');
```

## 🔒 Security Best Practices

✅ **Implemented:**
- PDO prepared statements (SQL injection prevention)
- Input sanitization (XSS prevention)
- Password hashing (bcrypt)
- Session management
- Activity logging

⚠️ **Recommended for Production:**
- Enable HTTPS/SSL
- Disable error display
- Use strong database passwords
- Set restrictive file permissions
- Regular backups
- Keep PHP and MySQL updated

## 📊 Sample Data

The database includes sample data for testing:
- 3 clients
- 4 domains
- 3 hosting accounts
- 5 payments

Delete or modify after testing.

## 🎨 Customization

### Change Colors

Edit CSS variables in `views/layouts/header.php`:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
}
```

### Add New Modules

1. Create model in `models/`
2. Create controller in `controllers/`
3. Create views in `views/`
4. Add route in `index.php`
5. Add menu item in `views/layouts/header.php`

## 🐛 Troubleshooting

### Common Issues:

**Database Connection Error**
- Check credentials in `config/database.php`
- Verify MySQL is running
- Ensure database exists

**Blank Page**
- Enable error reporting temporarily
- Check PHP error logs
- Verify file permissions

**Login Issues**
- Verify database import
- Check users table has admin user
- Clear browser cache/cookies

For more troubleshooting, see [INSTALLATION.md](INSTALLATION.md)

## 📈 Future Enhancements

Potential features to add:
- Email notifications (SMTP integration)
- PDF invoice generation
- Multi-language support
- API endpoints
- Advanced reporting
- Client portal
- Automated backups
- Two-factor authentication
- Domain/hosting auto-renewal integration

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

Created with ❤️ for managing SaaS business operations efficiently.

## 🙏 Acknowledgments

- Bootstrap team for the amazing CSS framework
- Chart.js for beautiful charts
- Bootstrap Icons for comprehensive icon set
- PHP community for excellent documentation

## 📞 Support

For support and questions:
- Check [INSTALLATION.md](INSTALLATION.md) for setup help
- Review troubleshooting section above
- Check PHP error logs for debugging

## ⭐ Show Your Support

If you find this project helpful, please give it a star!

---

**Built with PHP, MySQL, and Bootstrap** | **MVC Architecture** | **Production Ready**