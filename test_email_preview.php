<?php
/**
 * Email Preview Test
 * 
 * This script generates and displays the email HTML for testing
 * Run this to see what the emails look like
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
$clientModel = new Client();

echo "=== Email Preview Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Get domains expiring in 10 days or less (WITHOUT the duplicate check)
$sql = "
    SELECT d.*, c.name as client_name, c.email as client_email, c.company_name,
           DATEDIFF(d.expiry_date, CURDATE()) as days_left
    FROM domains d
    INNER JOIN clients c ON d.client_id = c.id
    WHERE d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)
        AND d.status = 'active'
";

try {
    $db = getDBConnection();
    $stmt = $db->query($sql);
    $domains = $stmt->fetchAll();
    
    echo "Found " . count($domains) . " domain(s) expiring in 10 days or less\n\n";
    
    if (count($domains) > 0) {
        foreach ($domains as $domain) {
            $daysLeft = $domain['days_left'];
            echo "Domain: {$domain['domain_name']}\n";
            echo "Client: {$domain['client_name']}\n";
            echo "Days Left: {$daysLeft}\n";
            echo "Expiry Date: {$domain['expiry_date']}\n\n";
            
            $client = [
                'name' => $domain['client_name'],
                'email' => $domain['client_email'],
                'company_name' => $domain['company_name']
            ];
            
            // Generate email HTML
            $subject = "Domain Expiry Alert: {$domain['domain_name']} - {$daysLeft} days left";
            
            // Get the email template
            $reflection = new ReflectionClass($emailHelper);
            $method = $reflection->getMethod('getDomainExpiryEmailTemplate');
            $method->setAccessible(true);
            $emailHtml = $method->invoke($emailHelper, $domain, $client, $daysLeft);
            
            // Save to file
            $filename = "email_preview_domain_{$domain['id']}_" . date('Y-m-d_His') . ".html";
            file_put_contents($filename, $emailHtml);
            
            echo "✓ Email HTML saved to: {$filename}\n";
            echo "  Open this file in your browser to see the email\n\n";
            
            echo "Recipients:\n";
            foreach (NOTIFICATION_EMAILS as $email) {
                echo "  - {$email}\n";
            }
            echo "\n";
            
            echo "Subject: {$subject}\n";
            echo str_repeat("=", 70) . "\n\n";
        }
    } else {
        echo "No domains found expiring in the next 10 days.\n";
        echo "Try updating a domain's expiry date to test:\n\n";
        echo "UPDATE domains SET expiry_date = DATE_ADD(CURDATE(), INTERVAL 4 DAY) WHERE id = 1;\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Check the generated HTML files to see what the emails look like!\n";

// Made with Bob