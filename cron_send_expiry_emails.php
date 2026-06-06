<?php
/**
 * Cron Job: Send Expiry Email Notifications
 * 
 * This script should be run daily via cron job to send email notifications
 * for domains and hosting expiring in 10 days.
 * 
 * Cron setup example (run daily at 9 AM):
 * 0 9 * * * /usr/bin/php /path/to/saas_bms/cron_send_expiry_emails.php
 */

// Include configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/EmailHelperSMTP.php';
require_once __DIR__ . '/models/Domain.php';
require_once __DIR__ . '/models/Hosting.php';
require_once __DIR__ . '/models/Client.php';

// Initialize models
$emailHelper = new EmailHelperSMTP();
$domainModel = new Domain();
$hostingModel = new Hosting();
$clientModel = new Client();

$emailsSent = 0;
$emailsFailed = 0;

echo "=== Starting Expiry Email Notifications ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Get domains expiring in 10 days or less (but not expired yet)
$sql = "
    SELECT d.*, c.name as client_name, c.email as client_email, c.company_name,
           DATEDIFF(d.expiry_date, CURDATE()) as days_left
    FROM domains d
    INNER JOIN clients c ON d.client_id = c.id
    WHERE d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)
        AND d.status = 'active'
        AND NOT EXISTS (
            SELECT 1 FROM email_logs el
            WHERE el.recipient IN ('algorythms3141@gmail.com', 'joshuajeberson@gmail.com', 'sadiqsariq55@gmail.com')
            AND el.subject LIKE CONCAT('%', d.domain_name, '%')
            AND DATE(el.sent_at) = CURDATE()
        )
";

try {
    $db = getDBConnection();
    $stmt = $db->query($sql);
    $domains = $stmt->fetchAll();
    
    echo "Found " . count($domains) . " domain(s) expiring in 10 days or less\n";
    
    foreach ($domains as $domain) {
        $daysLeft = $domain['days_left'];
        echo "Processing domain: {$domain['domain_name']} for client {$domain['client_name']} ({$daysLeft} days left)... ";
        
        $client = [
            'name' => $domain['client_name'],
            'email' => $domain['client_email'],
            'company_name' => $domain['company_name']
        ];
        
        $success = $emailHelper->sendDomainExpiryNotification($domain, $client, $daysLeft);
        
        if ($success) {
            echo "✓ Email sent\n";
            $emailsSent++;
        } else {
            echo "✗ Email failed\n";
            $emailsFailed++;
        }
    }
    
} catch (Exception $e) {
    echo "Error processing domains: " . $e->getMessage() . "\n";
}

echo "\n";

// Get hosting expiring in 10 days or less (but not expired yet)
$sql = "
    SELECT h.*, c.name as client_name, c.email as client_email, c.company_name,
           DATEDIFF(h.expiry_date, CURDATE()) as days_left
    FROM hosting h
    INNER JOIN clients c ON h.client_id = c.id
    WHERE h.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)
        AND h.status = 'active'
        AND NOT EXISTS (
            SELECT 1 FROM email_logs el
            WHERE el.recipient IN ('algorythms3141@gmail.com', 'joshuajeberson@gmail.com', 'sadiqsariq55@gmail.com')
            AND el.subject LIKE CONCAT('%', h.server_name, '%')
            AND DATE(el.sent_at) = CURDATE()
        )
";

try {
    $stmt = $db->query($sql);
    $hostings = $stmt->fetchAll();
    
    echo "Found " . count($hostings) . " hosting service(s) expiring in 10 days or less\n";
    
    foreach ($hostings as $hosting) {
        $daysLeft = $hosting['days_left'];
        echo "Processing hosting: {$hosting['server_name']} for client {$hosting['client_name']} ({$daysLeft} days left)... ";
        
        $client = [
            'name' => $hosting['client_name'],
            'email' => $hosting['client_email'],
            'company_name' => $hosting['company_name']
        ];
        
        $success = $emailHelper->sendHostingExpiryNotification($hosting, $client, $daysLeft);
        
        if ($success) {
            echo "✓ Email sent\n";
            $emailsSent++;
        } else {
            echo "✗ Email failed\n";
            $emailsFailed++;
        }
    }
    
} catch (Exception $e) {
    echo "Error processing hosting: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "Emails sent successfully: {$emailsSent}\n";
echo "Emails failed: {$emailsFailed}\n";
echo "Total emails processed: " . ($emailsSent + $emailsFailed) . "\n";
echo "Completed at: " . date('Y-m-d H:i:s') . "\n";

// Made with Bob