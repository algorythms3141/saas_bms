<?php
/**
 * Hosting Model
 * 
 * Handles hosting data operations
 */

require_once __DIR__ . '/Model.php';

class Hosting extends Model {
    protected $table = 'hosting';
    
    /**
     * Get all hosting with client and domain info
     */
    public function getAllWithDetails($orderBy = 'h.expiry_date ASC', $limit = null, $offset = null) {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                c.email as client_email,
                c.company_name as client_company,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
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
     * Get hosting with full details
     */
    public function getHostingWithDetails($id) {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                c.email as client_email,
                c.phone as client_phone,
                c.company_name as client_company,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get hosting by client
     */
    public function getByClient($clientId) {
        $sql = "
            SELECT 
                h.*,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.client_id = ?
            ORDER BY h.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get expiring hosting
     */
    public function getExpiringHosting($days = 30) {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                c.email as client_email,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND h.status = 'active'
            ORDER BY h.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get expired hosting
     */
    public function getExpiredHosting() {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                c.email as client_email,
                d.domain_name,
                DATEDIFF(CURDATE(), h.expiry_date) as days_expired
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.expiry_date < CURDATE()
                AND h.status = 'active'
            ORDER BY h.expiry_date DESC
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Update expired hosting status
     */
    public function updateExpiredStatus() {
        $sql = "
            UPDATE {$this->table}
            SET status = 'expired'
            WHERE expiry_date < CURDATE() AND status = 'active'
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->rowCount();
    }
    
    /**
     * Get hosting by status
     */
    public function getByStatus($status) {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.status = ?
            ORDER BY h.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get hosting by server
     */
    public function getByServer($serverName) {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.server_name = ?
            ORDER BY h.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$serverName]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get hosting by plan
     */
    public function getByPlan($planName) {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.plan_name = ?
            ORDER BY h.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$planName]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search hosting
     */
    public function searchHosting($searchTerm, $limit = null, $offset = null) {
        $sql = "
            SELECT 
                h.*,
                c.name as client_name,
                c.email as client_email,
                d.domain_name,
                DATEDIFF(h.expiry_date, CURDATE()) as days_left
            FROM {$this->table} h
            INNER JOIN clients c ON h.client_id = c.id
            LEFT JOIN domains d ON h.domain_id = d.id
            WHERE h.server_name LIKE ? OR h.plan_name LIKE ? OR c.name LIKE ? OR d.domain_name LIKE ?
            ORDER BY h.expiry_date ASC
        ";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $searchParam = "%{$searchTerm}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get hosting statistics
     */
    public function getStatistics() {
        $sql = "
            SELECT 
                COUNT(*) as total_hosting,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_hosting,
                COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired_hosting,
                COUNT(CASE WHEN status = 'suspended' THEN 1 END) as suspended_hosting,
                COUNT(CASE WHEN expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status = 'active' THEN 1 END) as expiring_soon,
                COUNT(CASE WHEN expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = 'active' THEN 1 END) as expiring_urgent,
                COALESCE(SUM(cost), 0) as total_cost
            FROM {$this->table}
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    /**
     * Get all servers
     */
    public function getAllServers() {
        $sql = "SELECT DISTINCT server_name FROM {$this->table} ORDER BY server_name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Get all plans
     */
    public function getAllPlans() {
        $sql = "SELECT DISTINCT plan_name FROM {$this->table} ORDER BY plan_name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Create hosting with activity log
     */
    public function createHosting($data) {
        $hostingId = $this->insert($data);
        
        if ($hostingId) {
            logActivity('create', 'hosting', $hostingId, "Created hosting: {$data['server_name']} - {$data['plan_name']}");
        }
        
        return $hostingId;
    }
    
    /**
     * Update hosting with activity log
     */
    public function updateHosting($id, $data) {
        $result = $this->update($id, $data);
        
        if ($result) {
            logActivity('update', 'hosting', $id, "Updated hosting: {$data['server_name']} - {$data['plan_name']}");
        }
        
        return $result;
    }
    
    /**
     * Delete hosting with activity log
     */
    public function deleteHosting($id) {
        $hosting = $this->getById($id);
        $result = $this->delete($id);
        
        if ($result && $hosting) {
            logActivity('delete', 'hosting', $id, "Deleted hosting: {$hosting['server_name']} - {$hosting['plan_name']}");
        }
        
        return $result;
    }
}

// Made with Bob
