<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            font-size: 18px;
            width: 25px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .notification-icon {
            position: relative;
            font-size: 20px;
            color: #666;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .stat-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }
        
        .stat-card p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        
        /* Tables */
        .table-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-card h5 {
            margin: 0;
            font-weight: 600;
        }
        
        /* Badges */
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        /* Buttons */
        .btn {
            border-radius: 5px;
            padding: 8px 16px;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        /* Alert Colors */
        .alert-urgent {
            background-color: #dc3545;
            color: white;
        }
        
        .alert-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .alert-info {
            background-color: #17a2b8;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-shield-lock" style="font-size: 36px;"></i>
            <h4><?php echo APP_NAME; ?></h4>
            <small>v<?php echo APP_VERSION; ?></small>
        </div>
        
        <div class="sidebar-menu">
            <a href="index.php?page=dashboard" class="<?php echo ($page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="index.php?page=clients" class="<?php echo ($page ?? '') === 'clients' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                <span>Clients</span>
            </a>
            
            <a href="index.php?page=domains" class="<?php echo ($page ?? '') === 'domains' ? 'active' : ''; ?>">
                <i class="bi bi-globe"></i>
                <span>Domains</span>
            </a>
            
            <a href="index.php?page=hosting" class="<?php echo ($page ?? '') === 'hosting' ? 'active' : ''; ?>">
                <i class="bi bi-server"></i>
                <span>Hosting</span>
            </a>
            
            <a href="index.php?page=payments" class="<?php echo ($page ?? '') === 'payments' ? 'active' : ''; ?>">
                <i class="bi bi-credit-card"></i>
                <span>Payments</span>
            </a>
            
            <a href="index.php?page=notifications" class="<?php echo ($page ?? '') === 'notifications' ? 'active' : ''; ?>">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </a>
            
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
            
            <a href="index.php?page=logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <h1 class="page-title"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
            
            <div class="user-menu">
                <a href="index.php?page=notifications" class="notification-icon">
                    <i class="bi bi-bell"></i>
                    <?php
                    require_once __DIR__ . '/../../models/Notification.php';
                    $notificationModel = new Notification();
                    $unreadCount = $notificationModel->getUnreadCount(getCurrentUserId());
                    if ($unreadCount > 0):
                    ?>
                        <span class="notification-badge"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <?php 
                        $user = getCurrentUser();
                        echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); 
                        ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 14px;">
                            <?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?>
                        </div>
                        <div style="font-size: 12px; color: #666;">
                            <?php echo ucfirst($user['role'] ?? 'staff'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <?php
            // Display flash messages
            $flash = getFlashMessage();
            if ($flash):
            ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

// Made with Bob
