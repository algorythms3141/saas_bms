<?php
/**
 * Main Entry Point - Router
 * 
 * This file handles all incoming requests and routes them to appropriate controllers
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Get the requested page
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

// Define routes
$routes = [
    // Authentication routes
    'login' => ['file' => 'controllers/AuthController.php', 'class' => 'AuthController', 'public' => true],
    'logout' => ['file' => 'controllers/AuthController.php', 'class' => 'AuthController', 'public' => true],
    
    // Dashboard
    'dashboard' => ['file' => 'controllers/DashboardController.php', 'class' => 'DashboardController'],
    
    // Client routes
    'clients' => ['file' => 'controllers/ClientController.php', 'class' => 'ClientController'],
    
    // Domain routes
    'domains' => ['file' => 'controllers/DomainController.php', 'class' => 'DomainController'],
    
    // Hosting routes
    'hosting' => ['file' => 'controllers/HostingController.php', 'class' => 'HostingController'],
    
    // Payment routes
    'payments' => ['file' => 'controllers/PaymentController.php', 'class' => 'PaymentController'],
    
    // Notification routes
    'notifications' => ['file' => 'controllers/NotificationController.php', 'class' => 'NotificationController'],
];

// Check if route exists
if (!isset($routes[$page])) {
    // Default to dashboard if logged in, otherwise login
    $page = isLoggedIn() ? 'dashboard' : 'login';
}

$route = $routes[$page];

// Check authentication
if (!isset($route['public']) || !$route['public']) {
    requireLogin();
}

// Load and instantiate controller
if (file_exists($route['file'])) {
    require_once $route['file'];
    
    $controllerClass = $route['class'];
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        
        // Call the action method
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            // Default to index if action doesn't exist
            if (method_exists($controller, 'index')) {
                $controller->index();
            } else {
                die("Action '{$action}' not found in controller '{$controllerClass}'");
            }
        }
    } else {
        die("Controller class '{$controllerClass}' not found");
    }
} else {
    die("Controller file '{$route['file']}' not found");
}

// Made with Bob
