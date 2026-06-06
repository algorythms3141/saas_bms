<?php
/**
 * Notification Controller
 * 
 * Handles notification management operations
 */

require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $notificationModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
    }
    
    /**
     * List all notifications
     */
    public function index() {
        $userId = $_SESSION['user_id'] ?? 0;
        
        // Get filter parameters
        $filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';
        
        // Get notifications based on filter
        switch ($filter) {
            case 'unread':
                $notifications = $this->notificationModel->getUnreadByUser($userId);
                break;
            case 'urgent':
                $notifications = $this->notificationModel->getByPriority('urgent', $userId);
                break;
            case 'high':
                $notifications = $this->notificationModel->getByPriority('high', $userId);
                break;
            case 'domain':
                $notifications = $this->notificationModel->getByType('domain_expiry', $userId);
                break;
            case 'hosting':
                $notifications = $this->notificationModel->getByType('hosting_expiry', $userId);
                break;
            default:
                $notifications = $this->notificationModel->getByUser($userId);
                break;
        }
        
        $pageTitle = 'Notifications';
        $page = 'notifications';
        
        require_once __DIR__ . '/../views/notifications/index.php';
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=notifications');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid notification ID');
            redirect('/index.php?page=notifications');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=notifications');
        }
        
        $result = $this->notificationModel->markAsRead($id);
        
        if ($result) {
            setFlashMessage('success', 'Notification marked as read');
        } else {
            setFlashMessage('danger', 'Failed to mark notification as read');
        }
        
        redirect('/index.php?page=notifications');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=notifications');
        }
        
        $userId = $_SESSION['user_id'] ?? 0;
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=notifications');
        }
        
        $result = $this->notificationModel->markAllAsRead($userId);
        
        if ($result) {
            setFlashMessage('success', 'All notifications marked as read');
        } else {
            setFlashMessage('info', 'No unread notifications');
        }
        
        redirect('/index.php?page=notifications');
    }
    
    /**
     * Delete notification
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=notifications');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid notification ID');
            redirect('/index.php?page=notifications');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=notifications');
        }
        
        $result = $this->notificationModel->delete($id);
        
        if ($result) {
            setFlashMessage('success', 'Notification deleted successfully');
        } else {
            setFlashMessage('danger', 'Failed to delete notification');
        }
        
        redirect('/index.php?page=notifications');
    }
    
    /**
     * Generate expiry notifications
     */
    public function generateExpiry() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=notifications');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=notifications');
        }
        
        $count = $this->notificationModel->generateExpiryNotifications();
        
        if ($count > 0) {
            setFlashMessage('success', "Generated {$count} expiry notifications");
        } else {
            setFlashMessage('info', 'No new expiry notifications to generate');
        }
        
        redirect('/index.php?page=notifications');
    }
    
    /**
     * Delete old notifications
     */
    public function deleteOld() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=notifications');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=notifications');
        }
        
        $days = isset($_POST['days']) ? (int)$_POST['days'] : 30;
        $result = $this->notificationModel->deleteOldNotifications($days);
        
        if ($result) {
            setFlashMessage('success', 'Old notifications deleted successfully');
        } else {
            setFlashMessage('info', 'No old notifications to delete');
        }
        
        redirect('/index.php?page=notifications');
    }
}

// Made with Bob