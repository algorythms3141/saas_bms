<?php
/**
 * Notification Model
 * 
 * Handles notification data operations
 */

require_once __DIR__ . '/Model.php';

class Notification extends Model {
    protected $table = 'notifications';
    
    /**
     * Get all notifications for a user
     */
    public function getByUser($userId, $limit = null) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? OR user_id IS NULL ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get unread notifications for a user
     */
    public function getUnreadByUser($userId) {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0 
            ORDER BY priority DESC, created_at DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get unread count for a user
     */
    public function getUnreadCount($userId) {
        $sql = "
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_read = 1 
            WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0
        ");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Get notifications by type
     */
    public function getByType($type, $userId = null) {
        if ($userId) {
            $sql = "
                SELECT * FROM {$this->table} 
                WHERE type = ? AND (user_id = ? OR user_id IS NULL)
                ORDER BY created_at DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$type, $userId]);
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE type = ? ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$type]);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get notifications by priority
     */
    public function getByPriority($priority, $userId = null) {
        if ($userId) {
            $sql = "
                SELECT * FROM {$this->table} 
                WHERE priority = ? AND (user_id = ? OR user_id IS NULL)
                ORDER BY created_at DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$priority, $userId]);
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE priority = ? ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$priority]);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Create notification
     */
    public function createNotification($data) {
        return $this->insert($data);
    }
    
    /**
     * Delete old notifications
     */
    public function deleteOldNotifications($days = 30) {
        $sql = "
            DELETE FROM {$this->table} 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY) AND is_read = 1
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$days]);
    }
    
    /**
     * Generate expiry notifications
     */
    public function generateExpiryNotifications() {
        // Get expiring domains
        $domainSql = "
            SELECT 
                d.id,
                d.domain_name,
                d.expiry_date,
                c.name as client_name,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM domains d
            INNER JOIN clients c ON d.client_id = c.id
            WHERE d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                AND d.status = 'active'
                AND NOT EXISTS (
                    SELECT 1 FROM {$this->table} n 
                    WHERE n.related_type = 'domain' 
                        AND n.related_id = d.id 
                        AND DATE(n.created_at) = CURDATE()
                )
        ";
        
        $stmt = $this->db->query($domainSql);
        $domains = $stmt->fetchAll();
        
        foreach ($domains as $domain) {
            $priority = 'medium';
            if ($domain['days_left'] <= 7) {
                $priority = 'urgent';
            } elseif ($domain['days_left'] <= 15) {
                $priority = 'high';
            }
            
            $this->createNotification([
                'user_id' => null, // Visible to all users
                'type' => 'domain_expiry',
                'title' => 'Domain Expiring: ' . $domain['domain_name'],
                'message' => "Domain {$domain['domain_name']} for client {$domain['client_name']} expires on " . formatDate($domain['expiry_date']) . " ({$domain['days_left']} days left)",
                'related_type' => 'domain',
                'related_id' => $domain['id'],
                'priority' => $priority
            ]);
        }
        
        // Get expiring hosting
        $hostingSql = "
            SELECT 
                h.id,
                h.server_name,
                h.plan_name,
                h.expiry_date,
                c.name as client_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM hosting h
            INNER JOIN clients c ON h.client_id = c.id
            WHERE h.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                AND h.status = 'active'
                AND NOT EXISTS (
                    SELECT 1 FROM {$this->table} n 
                    WHERE n.related_type = 'hosting' 
                        AND n.related_id = h.id 
                        AND DATE(n.created_at) = CURDATE()
                )
        ";
        
        $stmt = $this->db->query($hostingSql);
        $hostings = $stmt->fetchAll();
        
        foreach ($hostings as $hosting) {
            $priority = 'medium';
            if ($hosting['days_left'] <= 7) {
                $priority = 'urgent';
            } elseif ($hosting['days_left'] <= 15) {
                $priority = 'high';
            }
            
            $this->createNotification([
                'user_id' => null, // Visible to all users
                'type' => 'hosting_expiry',
                'title' => 'Hosting Expiring: ' . $hosting['server_name'],
                'message' => "Hosting {$hosting['plan_name']} on {$hosting['server_name']} for client {$hosting['client_name']} expires on " . formatDate($hosting['expiry_date']) . " ({$hosting['days_left']} days left)",
                'related_type' => 'hosting',
                'related_id' => $hosting['id'],
                'priority' => $priority
            ]);
        }
        
        return count($domains) + count($hostings);
    }
    
    /**
     * Get recent notifications
     */
    public function getRecentNotifications($userId, $limit = 5) {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE user_id = ? OR user_id IS NULL 
            ORDER BY created_at DESC 
            LIMIT ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}

// Made with Bob
