<?php
/**
 * Password Test and Reset Script
 * 
 * Use this to verify password hashing and reset admin password if needed
 */

// Test password
$password = 'admin123';

// Generate hash
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Test</h2>";
echo "<p><strong>Plain Password:</strong> $password</p>";
echo "<p><strong>Generated Hash:</strong> $hash</p>";

// Test verification with the hash from database
$dbHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "<p><strong>Database Hash:</strong> $dbHash</p>";

if (password_verify($password, $dbHash)) {
    echo "<p style='color: green;'><strong>✓ Password verification SUCCESSFUL!</strong></p>";
    echo "<p>The password 'admin123' matches the database hash.</p>";
} else {
    echo "<p style='color: red;'><strong>✗ Password verification FAILED!</strong></p>";
    echo "<p>The password does not match. Use the SQL below to reset:</p>";
    echo "<pre>";
    echo "UPDATE users SET password = '$hash' WHERE username = 'admin';";
    echo "</pre>";
}

echo "<hr>";
echo "<h3>To Reset Admin Password:</h3>";
echo "<p>Run this SQL in phpMyAdmin:</p>";
echo "<pre>";
echo "UPDATE users SET password = '$hash' WHERE username = 'admin';";
echo "</pre>";

echo "<hr>";
echo "<h3>Alternative: Create New Hash</h3>";
echo "<p>If you want a different password, change the \$password variable at the top of this file and refresh.</p>";
?>

// Made with Bob
