<?php
/**
 * Domain Model
 * 
 * Handles domain data operations
 */

require_once __DIR__ . '/Model.php';

class Domain extends Model {
    protected $table = 'domains';
    
    /**
     * Get all domains with client info
     */
    public function getAllWithClient($orderBy = 'd.expiry_date ASC', $limit = null, $offset = null) {
        $sql = "
            SELECT 
                d.*,
                c.name as client_name,
                c.email as client_email,
                c.company_name as client_company,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM {$this->table} d
            INNER JOIN clients c ON d.client_id = c.id
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
     * Get domain with client info
     */
    public function getDomainWithClient($id) {
        $sql = "
            SELECT 
                d.*,
                c.name as client_name,
                c.email as client_email,
                c.phone as client_phone,
                c.company_name as client_company,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM {$this->table} d
            INNER JOIN clients c ON d.client_id = c.id
            WHERE d.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get domains by client
     */
    public function getByClient($clientId) {
        $sql = "
            SELECT 
                d.*,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM {$this->table} d
            WHERE d.client_id = ?
            ORDER BY d.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get expiring domains
     */
    public function getExpiringDomains($days = 30) {
        $sql = "
            SELECT 
                d.*,
                c.name as client_name,
                c.email as client_email,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM {$this->table} d
            INNER JOIN clients c ON d.client_id = c.id
            WHERE d.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND d.status = 'active'
            ORDER BY d.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get expired domains
     */
    public function getExpiredDomains() {
        $sql = "
            SELECT 
                d.*,
                c.name as client_name,
                c.email as client_email,
                DATEDIFF(CURDATE(), d.expiry_date) as days_expired
            FROM {$this->table} d
            INNER JOIN clients c ON d.client_id = c.id
            WHERE d.expiry_date < CURDATE()
                AND d.status = 'active'
            ORDER BY d.expiry_date DESC
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Update expired domains status
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
     * Get domains by status
     */
    public function getByStatus($status) {
        $sql = "
            SELECT 
                d.*,
                c.name as client_name,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM {$this->table} d
            INNER JOIN clients c ON d.client_id = c.id
            WHERE d.status = ?
            ORDER BY d.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get domains by provider
     */
    public function getByProvider($provider) {
        $sql = "
            SELECT 
                d.*,
                c.name as client_name,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM {$this->table} d
            INNER JOIN clients c ON d.client_id = c.id
            WHERE d.provider = ?
            ORDER BY d.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$provider]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search domains
     */
    public function searchDomains($searchTerm, $limit = null, $offset = null) {
        $sql = "
            SELECT 
                d.*,
                c.name as client_name,
                c.email as client_email,
                DATEDIFF(d.expiry_date, CURDATE()) as days_left
            FROM {$this->table} d
            INNER JOIN clients c ON d.client_id = c.id
            WHERE d.domain_name LIKE ? OR c.name LIKE ? OR d.provider LIKE ?
            ORDER BY d.expiry_date ASC
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
     * Get domain statistics
     */
    public function getStatistics() {
        $sql = "
            SELECT 
                COUNT(*) as total_domains,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_domains,
                COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired_domains,
                COUNT(CASE WHEN expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status = 'active' THEN 1 END) as expiring_soon,
                COUNT(CASE WHEN expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = 'active' THEN 1 END) as expiring_urgent,
                COALESCE(SUM(cost), 0) as total_cost
            FROM {$this->table}
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    /**
     * Check if domain name exists
     */
    public function domainExists($domainName, $excludeId = null) {
        return $this->exists('domain_name', $domainName, $excludeId);
    }
    
    /**
     * Create domain with activity log
     */
    public function createDomain($data) {
        $domainId = $this->insert($data);
        
        if ($domainId) {
            logActivity('create', 'domains', $domainId, "Created domain: {$data['domain_name']}");
        }
        
        return $domainId;
    }
    
    /**
     * Update domain with activity log
     */
    public function updateDomain($id, $data) {
        $result = $this->update($id, $data);
        
        if ($result) {
            logActivity('update', 'domains', $id, "Updated domain: {$data['domain_name']}");
        }
        
        return $result;
    }
    
    /**
     * Delete domain with activity log
     */
    public function deleteDomain($id) {
        $domain = $this->getById($id);
        $result = $this->delete($id);
        
        if ($result && $domain) {
            logActivity('delete', 'domains', $id, "Deleted domain: {$domain['domain_name']}");
        }
        
        return $result;
    }
    
    /**
     * Get all providers
     */
    public function getAllProviders() {
        $sql = "SELECT DISTINCT provider FROM {$this->table} ORDER BY provider ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Made with Bob
