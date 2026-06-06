<?php
/**
 * Database Configuration
 * 
 * This file contains database connection settings.
 * Update these values according to your hosting environment.
 */

// Database Configuration
define('DB_HOST', 'localhost:3306');           // Database host (usually 'localhost')
define('DB_NAME', 'adalgorythm_bms');           // Database name
define('DB_USER', 'adalgorythm_saas_bms');                // Database username
define('DB_PASS', '4^ho1lSR?7woodLo');                    // Database password
define('DB_CHARSET', 'utf8mb4');         // Character set

/**
 * Get Database Connection
 * 
 * @return PDO Database connection object
 * @throws PDOException If connection fails
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error and show user-friendly message
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please contact the administrator.");
        }
    }
    
    return $pdo;
}

/**
 * Close Database Connection
 */
function closeDBConnection() {
    $pdo = null;
}

// Made with Bob
