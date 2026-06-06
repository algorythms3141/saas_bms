<?php
/**
 * Authentication Controller
 * 
 * Handles user authentication (login, logout)
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Show login page
     */
    public function index() {
        // If already logged in, redirect to dashboard
        if (isLoggedIn()) {
            redirect('/index.php?page=dashboard');
        }
        
        $this->login();
    }
    
    /**
     * Handle login
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (isLoggedIn()) {
            redirect('/index.php?page=dashboard');
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password';
            } else {
                $user = $this->userModel->authenticate($username, $password);
                
                if ($user) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user'] = $user;
                    
                    // Update last login
                    $this->userModel->updateLastLogin($user['id']);
                    
                    // Log activity
                    logActivity('login', 'auth', $user['id'], 'User logged in');
                    
                    // Redirect to dashboard
                    redirect('/index.php?page=dashboard');
                } else {
                    $error = 'Invalid username or password';
                }
            }
        }
        
        // Load login view
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        if (isLoggedIn()) {
            // Log activity
            logActivity('logout', 'auth', getCurrentUserId(), 'User logged out');
            
            // Destroy session
            session_unset();
            session_destroy();
        }
        
        redirect('/index.php?page=login');
    }
}

// Made with Bob
