<?php
/**
 * User Model
 * 
 * Handles user authentication and management
 */

require_once __DIR__ . '/Model.php';

class User extends Model {
    protected $table = 'users';
    
    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE (username = ? OR email = ?) AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Remove password from user data
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], HASH_ALGORITHM);
        }
        
        return $this->insert($data);
    }
    
    /**
     * Update user
     */
    public function updateUser($id, $data) {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], HASH_ALGORITHM);
        } else {
            unset($data['password']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        return $this->exists('username', $username, $excludeId);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        return $this->exists('email', $email, $excludeId);
    }
    
    /**
     * Get user by username
     */
    public function getByUsername($username) {
        return $this->getOneBy('username', $username);
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        return $this->getOneBy('email', $email);
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin($userId) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, HASH_ALGORITHM);
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET password = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    
    /**
     * Get active users
     */
    public function getActiveUsers() {
        return $this->getBy('status', 'active');
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role) {
        return $this->getBy('role', $role);
    }
}

// Made with Bob
