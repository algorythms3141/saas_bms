<?php
/**
 * Email Helper with SMTP Support
 * 
 * Uses Gmail SMTP to send emails reliably
 */
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelperSMTP {
    private $fromEmail;
    private $fromName;
    private $smtpHost = 'smtp.gmail.com';
    private $smtpPort = 587;
    private $smtpUsername;
    private $smtpPassword;
    
    public function __construct() {
        $this->fromEmail = SMTP_FROM_EMAIL;
        $this->fromName = SMTP_FROM_NAME;
        $this->smtpUsername = SMTP_USERNAME;
        $this->smtpPassword = SMTP_PASSWORD;
    }
    
    /**
     * Send email using SMTP
     */
    public function send($to, $subject, $body, $isHtml = true)
	{
		try {

			$mail = new PHPMailer(true);

			$mail->isSMTP();
			$mail->SMTPOptions = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				]
			];
			$mail->Host = SMTP_HOST;
			$mail->SMTPAuth = true;
			$mail->Username = SMTP_USERNAME;
			$mail->Password = SMTP_PASSWORD;
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port = SMTP_PORT;

			$mail->setFrom(
				SMTP_FROM_EMAIL,
				SMTP_FROM_NAME
			);

			$mail->addAddress($to);

			$mail->isHTML($isHtml);
			$mail->Subject = $subject;
			$mail->Body = $body;

			$success = $mail->send();

			$this->logEmail($to, $subject, $success);

			return $success;

		} catch (Exception $e) {

			error_log($mail->ErrorInfo);

			$this->logEmail($to, $subject, false);

			return false;
		}
	}   
    
    /**
     * Send email via socket connection to Gmail SMTP
     */
   
    
    /**
     * Send domain expiry notification email
     */
    public function sendDomainExpiryNotification($domain, $client, $daysLeft) {
        $subject = "Domain Expiry Alert: {$domain['domain_name']} - {$daysLeft} days left";
        $body = $this->getDomainExpiryEmailTemplate($domain, $client, $daysLeft);
        
        // Send to all notification recipients
        $success = true;
        foreach (NOTIFICATION_EMAILS as $email) {
            $result = $this->send($email, $subject, $body, true);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Send hosting expiry notification email
     */
    public function sendHostingExpiryNotification($hosting, $client, $daysLeft) {
        $subject = "Hosting Expiry Alert: {$hosting['server_name']} - {$daysLeft} days left";
        $body = $this->getHostingExpiryEmailTemplate($hosting, $client, $daysLeft);
        
        // Send to all notification recipients
        $success = true;
        foreach (NOTIFICATION_EMAILS as $email) {
            $result = $this->send($email, $subject, $body, true);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Get domain expiry email template
     */
    private function getDomainExpiryEmailTemplate($domain, $client, $daysLeft) {
        $urgencyColor = $daysLeft <= 7 ? '#dc3545' : ($daysLeft <= 15 ? '#ffc107' : '#0dcaf0');
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border: 1px solid #dee2e6; }
                .alert { padding: 15px; margin: 20px 0; border-radius: 5px; background: {$urgencyColor}; color: white; }
                .details { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; border: 1px solid #dee2e6; }
                .detail-row { padding: 10px 0; border-bottom: 1px solid #eee; }
                .detail-label { font-weight: bold; color: #666; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .button { display: inline-block; padding: 12px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌐 Domain Expiry Alert</h1>
                </div>
                
                <div class='content'>
                    <div class='alert'>
                        <h2 style='margin: 0;'>⚠️ Domain Expiring in {$daysLeft} Days!</h2>
                    </div>
                    
                    <p>Hello,</p>
                    <p>This is an automated reminder that the following domain is expiring soon:</p>
                    
                    <div class='details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Domain Name:</span>
                            <strong>{$domain['domain_name']}</strong>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Client:</span>
                            {$client['name']} ({$client['company_name']})
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Client Email:</span>
                            {$client['email']}
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Provider:</span>
                            {$domain['provider']}
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Expiry Date:</span>
                            <strong style='color: {$urgencyColor};'>" . formatDate($domain['expiry_date']) . "</strong>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Days Left:</span>
                            <strong style='color: {$urgencyColor};'>{$daysLeft} days</strong>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Auto Renew:</span>
                            " . ($domain['auto_renew'] ? '✅ Yes' : '❌ No') . "
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Cost:</span>
                            " . formatCurrency($domain['cost']) . "
                        </div>
                    </div>
                    
                    <p><strong>Action Required:</strong></p>
                    <ul>
                        <li>Contact the client to confirm renewal</li>
                        <li>Process payment if not auto-renewing</li>
                        <li>Update domain status in the system</li>
                    </ul>
                    
                    <center>
                        <a href='" . BASE_URL . "/index.php?page=domains&action=edit&id={$domain['id']}' class='button'>
                            View Domain Details
                        </a>
                    </center>
                </div>
                
                <div class='footer'>
                    <p>This is an automated email from " . APP_NAME . "</p>
                    <p>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Get hosting expiry email template
     */
    private function getHostingExpiryEmailTemplate($hosting, $client, $daysLeft) {
        $urgencyColor = $daysLeft <= 7 ? '#dc3545' : ($daysLeft <= 15 ? '#ffc107' : '#0dcaf0');
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #6f42c1; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border: 1px solid #dee2e6; }
                .alert { padding: 15px; margin: 20px 0; border-radius: 5px; background: {$urgencyColor}; color: white; }
                .details { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; border: 1px solid #dee2e6; }
                .detail-row { padding: 10px 0; border-bottom: 1px solid #eee; }
                .detail-label { font-weight: bold; color: #666; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .button { display: inline-block; padding: 12px 30px; background: #6f42c1; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🖥️ Hosting Expiry Alert</h1>
                </div>
                
                <div class='content'>
                    <div class='alert'>
                        <h2 style='margin: 0;'>⚠️ Hosting Expiring in {$daysLeft} Days!</h2>
                    </div>
                    
                    <p>Hello,</p>
                    <p>This is an automated reminder that the following hosting service is expiring soon:</p>
                    
                    <div class='details'>
                        <div class='detail-row'>
                            <span class='detail-label'>Server Name:</span>
                            <strong>{$hosting['server_name']}</strong>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Plan:</span>
                            {$hosting['plan_name']}
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Client:</span>
                            {$client['name']} ({$client['company_name']})
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Expiry Date:</span>
                            <strong style='color: {$urgencyColor};'>" . formatDate($hosting['expiry_date']) . "</strong>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Days Left:</span>
                            <strong style='color: {$urgencyColor};'>{$daysLeft} days</strong>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Auto Renew:</span>
                            " . ($hosting['auto_renew'] ? '✅ Yes' : '❌ No') . "
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Cost:</span>
                            " . formatCurrency($hosting['cost']) . "
                        </div>
                    </div>
                    
                    <p><strong>Action Required:</strong></p>
                    <ul>
                        <li>Contact the client to confirm renewal</li>
                        <li>Process payment if not auto-renewing</li>
                        <li>Update hosting status in the system</li>
                        <li>Ensure data backup before expiry</li>
                    </ul>
                    
                    <center>
                        <a href='" . BASE_URL . "/index.php?page=hosting&action=edit&id={$hosting['id']}' class='button'>
                            View Hosting Details
                        </a>
                    </center>
                </div>
                
                <div class='footer'>
                    <p>This is an automated email from " . APP_NAME . "</p>
                    <p>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Log email sending attempt
     */
    private function logEmail($to, $subject, $success) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("
                INSERT INTO email_logs (recipient, subject, status, sent_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([
                $to,
                $subject,
                $success ? 'sent' : 'failed'
            ]);
        } catch (Exception $e) {
            error_log("Email Log Error: " . $e->getMessage());
        }
    }
}

// Made with Bob