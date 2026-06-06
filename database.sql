-- =====================================================
-- SaaS Business Management System - Database Schema
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS saas_bms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saas_bms;

-- =====================================================
-- Table: users
-- Purpose: Store admin/staff user accounts
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: clients
-- Purpose: Store client information
-- =====================================================
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    company_name VARCHAR(150),
    address TEXT,
    notes TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_email (email),
    INDEX idx_status (status),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: domains
-- Purpose: Track domain registrations
-- =====================================================
CREATE TABLE IF NOT EXISTS domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    domain_name VARCHAR(255) NOT NULL,
    provider VARCHAR(100) NOT NULL COMMENT 'GoDaddy, Namecheap, etc.',
    purchase_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    cost DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    auto_renew BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'expired', 'pending') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_client_id (client_id),
    INDEX idx_domain_name (domain_name),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_status (status),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: hosting
-- Purpose: Track hosting subscriptions
-- =====================================================
CREATE TABLE IF NOT EXISTS hosting (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    domain_id INT,
    server_name VARCHAR(100) NOT NULL,
    plan_name VARCHAR(100) NOT NULL COMMENT 'Basic, Premium, Enterprise, etc.',
    start_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    cost DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    disk_space VARCHAR(50) COMMENT 'e.g., 10GB, Unlimited',
    bandwidth VARCHAR(50) COMMENT 'e.g., 100GB, Unlimited',
    auto_renew BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'expired', 'suspended') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_client_id (client_id),
    INDEX idx_domain_id (domain_id),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_status (status),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: payments
-- Purpose: Track all payments
-- =====================================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    service_type ENUM('domain', 'hosting', 'other') NOT NULL,
    service_id INT COMMENT 'Reference to domain_id or hosting_id',
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method VARCHAR(50) COMMENT 'Bank Transfer, PayPal, Credit Card, etc.',
    transaction_id VARCHAR(100),
    status ENUM('paid', 'pending', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_client_id (client_id),
    INDEX idx_service_type (service_type),
    INDEX idx_payment_date (payment_date),
    INDEX idx_status (status),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: notifications
-- Purpose: Store system notifications and alerts
-- =====================================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('domain_expiry', 'hosting_expiry', 'payment_due', 'system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_type ENUM('client', 'domain', 'hosting', 'payment'),
    related_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_priority (priority),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: activity_logs
-- Purpose: Track user activities for audit trail
-- =====================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL COMMENT 'create, update, delete, login, etc.',
    module VARCHAR(50) NOT NULL COMMENT 'clients, domains, hosting, etc.',
    record_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert Default Admin User
-- Username: admin
-- Password: admin123 (Please change after first login)
-- =====================================================
INSERT INTO users (username, email, password, full_name, role, status) 
VALUES (
    'admin', 
    'admin@saas-bms.local', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'System Administrator', 
    'admin', 
    'active'
);

-- =====================================================
-- Insert Sample Data (Optional - for testing)
-- =====================================================

-- Sample Clients
INSERT INTO clients (name, email, phone, company_name, status, created_by) VALUES
('John Doe', 'john@example.com', '+1234567890', 'Doe Enterprises', 'active', 1),
('Jane Smith', 'jane@example.com', '+1234567891', 'Smith Solutions', 'active', 1),
('Bob Johnson', 'bob@example.com', '+1234567892', 'Johnson Tech', 'active', 1);

-- Sample Domains
INSERT INTO domains (client_id, domain_name, provider, purchase_date, expiry_date, cost, status) VALUES
(1, 'doeenterprises.com', 'GoDaddy', '2025-01-15', '2027-01-15', 12.99, 'active'),
(1, 'doebusiness.net', 'Namecheap', '2025-03-20', '2026-03-20', 10.99, 'active'),
(2, 'smithsolutions.com', 'GoDaddy', '2024-06-10', '2026-06-10', 14.99, 'active'),
(3, 'johnsontech.io', 'Google Domains', '2025-02-01', '2026-05-01', 15.99, 'active');

-- Sample Hosting
INSERT INTO hosting (client_id, domain_id, server_name, plan_name, start_date, expiry_date, cost, disk_space, bandwidth, status) VALUES
(1, 1, 'Server-US-01', 'Premium Plan', '2025-01-15', '2027-01-15', 99.99, '50GB', 'Unlimited', 'active'),
(2, 3, 'Server-EU-02', 'Business Plan', '2024-06-10', '2026-06-10', 149.99, '100GB', 'Unlimited', 'active'),
(3, 4, 'Server-US-03', 'Basic Plan', '2025-02-01', '2026-05-01', 49.99, '20GB', '500GB', 'active');

-- Sample Payments
INSERT INTO payments (client_id, service_type, service_id, amount, payment_date, payment_method, status) VALUES
(1, 'domain', 1, 12.99, '2025-01-15', 'Credit Card', 'paid'),
(1, 'hosting', 1, 99.99, '2025-01-15', 'Credit Card', 'paid'),
(2, 'domain', 3, 14.99, '2024-06-10', 'PayPal', 'paid'),
(2, 'hosting', 2, 149.99, '2024-06-10', 'PayPal', 'paid'),
(3, 'domain', 4, 15.99, '2025-02-01', 'Bank Transfer', 'paid');

-- =====================================================
-- Views for Quick Access
-- =====================================================

-- View: Expiring Domains (Next 30 Days)
CREATE OR REPLACE VIEW v_expiring_domains AS
SELECT 
    d.id,
    d.domain_name,
    c.name AS client_name,
    c.email AS client_email,
    d.expiry_date,
    DATEDIFF(d.expiry_date, CURDATE()) AS days_left,
    d.cost,
    d.provider,
    d.status
FROM domains d
INNER JOIN clients c ON d.client_id = c.id
WHERE d.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND d.status = 'active'
ORDER BY d.expiry_date ASC;

-- View: Expiring Hosting (Next 30 Days)
CREATE OR REPLACE VIEW v_expiring_hosting AS
SELECT 
    h.id,
    h.server_name,
    h.plan_name,
    c.name AS client_name,
    c.email AS client_email,
    d.domain_name,
    h.expiry_date,
    DATEDIFF(h.expiry_date, CURDATE()) AS days_left,
    h.cost,
    h.status
FROM hosting h
INNER JOIN clients c ON h.client_id = c.id
LEFT JOIN domains d ON h.domain_id = d.id
WHERE h.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND h.status = 'active'
ORDER BY h.expiry_date ASC;

-- View: Client Summary
CREATE OR REPLACE VIEW v_client_summary AS
SELECT 
    c.id,
    c.name,
    c.email,
    c.company_name,
    COUNT(DISTINCT d.id) AS total_domains,
    COUNT(DISTINCT h.id) AS total_hosting,
    COALESCE(SUM(p.amount), 0) AS total_paid,
    c.status
FROM clients c
LEFT JOIN domains d ON c.id = d.client_id
LEFT JOIN hosting h ON c.id = h.client_id
LEFT JOIN payments p ON c.id = p.client_id AND p.status = 'paid'
GROUP BY c.id, c.name, c.email, c.company_name, c.status;

-- =====================================================
-- Stored Procedures
-- =====================================================

-- Procedure: Update Expired Services
DELIMITER //
CREATE PROCEDURE sp_update_expired_services()
BEGIN
    -- Update expired domains
    UPDATE domains 
    SET status = 'expired' 
    WHERE expiry_date < CURDATE() AND status = 'active';
    
    -- Update expired hosting
    UPDATE hosting 
    SET status = 'expired' 
    WHERE expiry_date < CURDATE() AND status = 'active';
END //
DELIMITER ;

-- Procedure: Generate Renewal Notifications
DELIMITER //
CREATE PROCEDURE sp_generate_renewal_notifications()
BEGIN
    -- Generate notifications for domains expiring in 30 days
    INSERT INTO notifications (user_id, type, title, message, related_type, related_id, priority)
    SELECT 
        1, -- Admin user
        'domain_expiry',
        CONCAT('Domain Expiring: ', d.domain_name),
        CONCAT('Domain ', d.domain_name, ' for client ', c.name, ' expires on ', DATE_FORMAT(d.expiry_date, '%Y-%m-%d'), ' (', DATEDIFF(d.expiry_date, CURDATE()), ' days left)'),
        'domain',
        d.id,
        CASE 
            WHEN DATEDIFF(d.expiry_date, CURDATE()) <= 7 THEN 'urgent'
            WHEN DATEDIFF(d.expiry_date, CURDATE()) <= 15 THEN 'high'
            ELSE 'medium'
        END
    FROM domains d
    INNER JOIN clients c ON d.client_id = c.id
    WHERE d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND d.status = 'active'
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.related_type = 'domain' 
                AND n.related_id = d.id 
                AND n.created_at >= CURDATE()
        );
    
    -- Generate notifications for hosting expiring in 30 days
    INSERT INTO notifications (user_id, type, title, message, related_type, related_id, priority)
    SELECT 
        1, -- Admin user
        'hosting_expiry',
        CONCAT('Hosting Expiring: ', h.server_name),
        CONCAT('Hosting ', h.plan_name, ' on ', h.server_name, ' for client ', c.name, ' expires on ', DATE_FORMAT(h.expiry_date, '%Y-%m-%d'), ' (', DATEDIFF(h.expiry_date, CURDATE()), ' days left)'),
        'hosting',
        h.id,
        CASE 
            WHEN DATEDIFF(h.expiry_date, CURDATE()) <= 7 THEN 'urgent'
            WHEN DATEDIFF(h.expiry_date, CURDATE()) <= 15 THEN 'high'
            ELSE 'medium'
        END
    FROM hosting h
    INNER JOIN clients c ON h.client_id = c.id
    WHERE h.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND h.status = 'active'
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.related_type = 'hosting' 
                AND n.related_id = h.id 
                AND n.created_at >= CURDATE()
        );
END //
DELIMITER ;

-- =====================================================
-- Triggers
-- =====================================================

-- Trigger: Log client creation
DELIMITER //
CREATE TRIGGER tr_client_after_insert
AFTER INSERT ON clients
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, module, record_id, description)
    VALUES (NEW.created_by, 'create', 'clients', NEW.id, CONCAT('Created client: ', NEW.name));
END //
DELIMITER ;

-- =====================================================
-- End of Database Schema
-- =====================================================

-- Made with Bob
