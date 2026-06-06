<?php
/**
 * Dashboard Controller
 * 
 * Handles dashboard display with statistics and charts
 */

require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Domain.php';
require_once __DIR__ . '/../models/Hosting.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Notification.php';

class DashboardController {
    private $clientModel;
    private $domainModel;
    private $hostingModel;
    private $paymentModel;
    private $notificationModel;
    
    public function __construct() {
        $this->clientModel = new Client();
        $this->domainModel = new Domain();
        $this->hostingModel = new Hosting();
        $this->paymentModel = new Payment();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Display dashboard
     */
    public function index() {
        // Update expired services
        $this->domainModel->updateExpiredStatus();
        $this->hostingModel->updateExpiredStatus();
        
        // Generate notifications for expiring services
        $this->notificationModel->generateExpiryNotifications();
        
        // Get statistics
        $stats = $this->getStatistics();
        
        // Get expiring services
        $expiringDomains = $this->domainModel->getExpiringDomains(30);
        $expiringHosting = $this->hostingModel->getExpiringHosting(30);
        
        // Get recent payments
        $recentPayments = $this->paymentModel->getRecentPayments(5);
        
        // Get recent notifications
        $recentNotifications = $this->notificationModel->getRecentNotifications(getCurrentUserId(), 5);
        
        // Get monthly revenue data for chart
        $monthlyRevenue = $this->paymentModel->getMonthlyRevenue();
        
        // Get revenue by service type for chart
        $revenueByService = $this->paymentModel->getRevenueByServiceType();
        
        // Set page variables
        $pageTitle = 'Dashboard';
        $page = 'dashboard';
        
        // Load view
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
    
    /**
     * Get dashboard statistics
     */
    private function getStatistics() {
        // Client statistics
        $totalClients = $this->clientModel->count();
        $activeClients = $this->clientModel->getCountByStatus('active');
        
        // Domain statistics
        $domainStats = $this->domainModel->getStatistics();
        
        // Hosting statistics
        $hostingStats = $this->hostingModel->getStatistics();
        
        // Payment statistics
        $paymentStats = $this->paymentModel->getStatistics();
        
        // Calculate monthly revenue (current month)
        $currentMonth = date('Y-m');
        $monthlyRevenueData = $this->paymentModel->getByDateRange(
            date('Y-m-01'),
            date('Y-m-t')
        );
        $monthlyRevenue = 0;
        foreach ($monthlyRevenueData as $payment) {
            if ($payment['status'] === 'paid') {
                $monthlyRevenue += $payment['amount'];
            }
        }
        
        return [
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'total_domains' => $domainStats['total_domains'],
            'active_domains' => $domainStats['active_domains'],
            'expired_domains' => $domainStats['expired_domains'],
            'expiring_domains_30' => $domainStats['expiring_soon'],
            'expiring_domains_7' => $domainStats['expiring_urgent'],
            'total_hosting' => $hostingStats['total_hosting'],
            'active_hosting' => $hostingStats['active_hosting'],
            'expired_hosting' => $hostingStats['expired_hosting'],
            'expiring_hosting_30' => $hostingStats['expiring_soon'],
            'expiring_hosting_7' => $hostingStats['expiring_urgent'],
            'total_payments' => $paymentStats['total_payments'],
            'paid_payments' => $paymentStats['paid_count'],
            'pending_payments' => $paymentStats['pending_count'],
            'total_revenue' => $paymentStats['total_paid'],
            'pending_amount' => $paymentStats['total_pending'],
            'monthly_revenue' => $monthlyRevenue
        ];
    }
}

// Made with Bob
