<?php
/**
 * Client Model
 * 
 * Handles client data operations
 */

require_once __DIR__ . '/Model.php';

class Client extends Model {
    protected $table = 'clients';
    
    /**
     * Get all clients with statistics
     */
    public function getAllWithStats($orderBy = 'name ASC', $limit = null, $offset = null) {
        $sql = "
            SELECT 
                c.*,
                COUNT(DISTINCT d.id) as total_domains,
                COUNT(DISTINCT h.id) as total_hosting,
                COALESCE(SUM(CASE WHEN p.status = 'paid' THEN p.amount ELSE 0 END), 0) as total_paid
            FROM {$this->table} c
            LEFT JOIN domains d ON c.id = d.client_id
            LEFT JOIN hosting h ON c.id = h.client_id
            LEFT JOIN payments p ON c.id = p.client_id
            GROUP BY c.id
        ";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get client with full details
     */
    public function getClientDetails($id) {
        $sql = "
            SELECT 
                c.*,
                COUNT(DISTINCT d.id) as total_domains,
                COUNT(DISTINCT h.id) as total_hosting,
                COALESCE(SUM(CASE WHEN p.status = 'paid' THEN p.amount ELSE 0 END), 0) as total_paid,
                COALESCE(SUM(CASE WHEN p.status = 'pending' THEN p.amount ELSE 0 END), 0) as pending_amount
            FROM {$this->table} c
            LEFT JOIN domains d ON c.id = d.client_id
            LEFT JOIN hosting h ON c.id = h.client_id
            LEFT JOIN payments p ON c.id = p.client_id
            WHERE c.id = ?
            GROUP BY c.id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Search clients
     */
    public function searchClients($searchTerm, $limit = null, $offset = null) {
        $sql = "
            SELECT 
                c.*,
                COUNT(DISTINCT d.id) as total_domains,
                COUNT(DISTINCT h.id) as total_hosting
            FROM {$this->table} c
            LEFT JOIN domains d ON c.id = d.client_id
            LEFT JOIN hosting h ON c.id = h.client_id
            WHERE c.name LIKE ? OR c.email LIKE ? OR c.company_name LIKE ?
            GROUP BY c.id
            ORDER BY c.name ASC
        ";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $searchParam = "%{$searchTerm}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchParam, $searchParam, $searchParam]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get active clients
     */
    public function getActiveClients() {
        return $this->getBy('status', 'active');
    }
    
    /**
     * Get clients with expiring services
     */
    public function getClientsWithExpiringServices($days = 30) {
        $sql = "
            SELECT DISTINCT c.*
            FROM {$this->table} c
            LEFT JOIN domains d ON c.id = d.client_id
            LEFT JOIN hosting h ON c.id = h.client_id
            WHERE (
                (d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) AND d.status = 'active')
                OR
                (h.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) AND h.status = 'active')
            )
            ORDER BY c.name ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days, $days]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        return $this->exists('email', $email, $excludeId);
    }
    
    /**
     * Get client count by status
     */
    public function getCountByStatus($status) {
        return $this->count('status = ?', [$status]);
    }
    
    /**
     * Create client with activity log
     */
    public function createClient($data) {
        $data['created_by'] = getCurrentUserId();
        $clientId = $this->insert($data);
        
        if ($clientId) {
            logActivity('create', 'clients', $clientId, "Created client: {$data['name']}");
        }
        
        return $clientId;
    }
    
    /**
     * Update client with activity log
     */
    public function updateClient($id, $data) {
        $result = $this->update($id, $data);
        
        if ($result) {
            logActivity('update', 'clients', $id, "Updated client: {$data['name']}");
        }
        
        return $result;
    }
    
    /**
     * Delete client with activity log
     */
    public function deleteClient($id) {
        $client = $this->getById($id);
        $result = $this->delete($id);
        
        if ($result && $client) {
            logActivity('delete', 'clients', $id, "Deleted client: {$client['name']}");
        }
        
        return $result;
    }
}

// Made with Bob
