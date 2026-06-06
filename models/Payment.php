<?php
/**
 * Payment Model
 * 
 * Handles payment data operations
 */

require_once __DIR__ . '/Model.php';

class Payment extends Model {
    protected $table = 'payments';
    
    /**
     * Get all payments with client info
     */
    public function getAllWithDetails($orderBy = 'p.payment_date DESC', $limit = null, $offset = null) {
        $sql = "
            SELECT 
                p.*,
                c.name as client_name,
                c.email as client_email,
                c.company_name as client_company,
                CASE 
                    WHEN p.service_type = 'domain' THEN d.domain_name
                    WHEN p.service_type = 'hosting' THEN CONCAT(h.server_name, ' - ', h.plan_name)
                    ELSE 'N/A'
                END as service_name
            FROM {$this->table} p
            INNER JOIN clients c ON p.client_id = c.id
            LEFT JOIN domains d ON p.service_type = 'domain' AND p.service_id = d.id
            LEFT JOIN hosting h ON p.service_type = 'hosting' AND p.service_id = h.id
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
     * Get payment with full details
     */
    public function getPaymentWithDetails($id) {
        $sql = "
            SELECT 
                p.*,
                c.name as client_name,
                c.email as client_email,
                c.phone as client_phone,
                c.company_name as client_company,
                CASE 
                    WHEN p.service_type = 'domain' THEN d.domain_name
                    WHEN p.service_type = 'hosting' THEN CONCAT(h.server_name, ' - ', h.plan_name)
                    ELSE 'N/A'
                END as service_name
            FROM {$this->table} p
            INNER JOIN clients c ON p.client_id = c.id
            LEFT JOIN domains d ON p.service_type = 'domain' AND p.service_id = d.id
            LEFT JOIN hosting h ON p.service_type = 'hosting' AND p.service_id = h.id
            WHERE p.id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get payments by client
     */
    public function getByClient($clientId) {
        $sql = "
            SELECT 
                p.*,
                CASE 
                    WHEN p.service_type = 'domain' THEN d.domain_name
                    WHEN p.service_type = 'hosting' THEN CONCAT(h.server_name, ' - ', h.plan_name)
                    ELSE 'N/A'
                END as service_name
            FROM {$this->table} p
            LEFT JOIN domains d ON p.service_type = 'domain' AND p.service_id = d.id
            LEFT JOIN hosting h ON p.service_type = 'hosting' AND p.service_id = h.id
            WHERE p.client_id = ?
            ORDER BY p.payment_date DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get payments by status
     */
    public function getByStatus($status) {
        $sql = "
            SELECT 
                p.*,
                c.name as client_name,
                c.email as client_email,
                CASE 
                    WHEN p.service_type = 'domain' THEN d.domain_name
                    WHEN p.service_type = 'hosting' THEN CONCAT(h.server_name, ' - ', h.plan_name)
                    ELSE 'N/A'
                END as service_name
            FROM {$this->table} p
            INNER JOIN clients c ON p.client_id = c.id
            LEFT JOIN domains d ON p.service_type = 'domain' AND p.service_id = d.id
            LEFT JOIN hosting h ON p.service_type = 'hosting' AND p.service_id = h.id
            WHERE p.status = ?
            ORDER BY p.payment_date DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get payments by service type
     */
    public function getByServiceType($serviceType) {
        $sql = "
            SELECT 
                p.*,
                c.name as client_name,
                CASE 
                    WHEN p.service_type = 'domain' THEN d.domain_name
                    WHEN p.service_type = 'hosting' THEN CONCAT(h.server_name, ' - ', h.plan_name)
                    ELSE 'N/A'
                END as service_name
            FROM {$this->table} p
            INNER JOIN clients c ON p.client_id = c.id
            LEFT JOIN domains d ON p.service_type = 'domain' AND p.service_id = d.id
            LEFT JOIN hosting h ON p.service_type = 'hosting' AND p.service_id = h.id
            WHERE p.service_type = ?
            ORDER BY p.payment_date DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$serviceType]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get payments by date range
     */
    public function getByDateRange($startDate, $endDate) {
        $sql = "
            SELECT 
                p.*,
                c.name as client_name,
                CASE 
                    WHEN p.service_type = 'domain' THEN d.domain_name
                    WHEN p.service_type = 'hosting' THEN CONCAT(h.server_name, ' - ', h.plan_name)
                    ELSE 'N/A'
                END as service_name
            FROM {$this->table} p
            INNER JOIN clients c ON p.client_id = c.id
            LEFT JOIN domains d ON p.service_type = 'domain' AND p.service_id = d.id
            LEFT JOIN hosting h ON p.service_type = 'hosting' AND p.service_id = h.id
            WHERE p.payment_date BETWEEN ? AND ?
            ORDER BY p.payment_date DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search payments
     */
    public function searchPayments($searchTerm, $limit = null, $offset = null) {
        $sql = "
            SELECT 
                p.*,
                c.name as client_name,
                c.email as client_email,
                CASE 
                    WHEN p.service_type = 'domain' THEN d.domain_name
                    WHEN p.service_type = 'hosting' THEN CONCAT(h.server_name, ' - ', h.plan_name)
                    ELSE 'N/A'
                END as service_name
            FROM {$this->table} p
            INNER JOIN clients c ON p.client_id = c.id
            LEFT JOIN domains d ON p.service_type = 'domain' AND p.service_id = d.id
            LEFT JOIN hosting h ON p.service_type = 'hosting' AND p.service_id = h.id
            WHERE c.name LIKE ? OR p.transaction_id LIKE ? OR p.payment_method LIKE ?
            ORDER BY p.payment_date DESC
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
     * Get payment statistics
     */
    public function getStatistics() {
        $sql = "
            SELECT 
                COUNT(*) as total_payments,
                COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_count,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_count,
                COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) as total_paid,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as total_pending,
                COALESCE(SUM(amount), 0) as total_amount
            FROM {$this->table}
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    /**
     * Get monthly revenue
     */
    public function getMonthlyRevenue($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $sql = "
            SELECT 
                MONTH(payment_date) as month,
                MONTHNAME(payment_date) as month_name,
                COUNT(*) as payment_count,
                COALESCE(SUM(amount), 0) as total_amount
            FROM {$this->table}
            WHERE YEAR(payment_date) = ? AND status = 'paid'
            GROUP BY MONTH(payment_date), MONTHNAME(payment_date)
            ORDER BY MONTH(payment_date)
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$year]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get revenue by service type
     */
    public function getRevenueByServiceType() {
        $sql = "
            SELECT 
                service_type,
                COUNT(*) as payment_count,
                COALESCE(SUM(amount), 0) as total_amount
            FROM {$this->table}
            WHERE status = 'paid'
            GROUP BY service_type
            ORDER BY total_amount DESC
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get pending payments
     */
    public function getPendingPayments() {
        return $this->getByStatus('pending');
    }
    
    /**
     * Get recent payments
     */
    public function getRecentPayments($limit = 10) {
        return $this->getAllWithDetails('p.payment_date DESC', $limit);
    }
    
    /**
     * Create payment with activity log
     */
    public function createPayment($data) {
        $paymentId = $this->insert($data);
        
        if ($paymentId) {
            logActivity('create', 'payments', $paymentId, "Created payment: " . formatCurrency($data['amount']) . " - {$data['status']}");
        }
        
        return $paymentId;
    }
    
    /**
     * Update payment with activity log
     */
    public function updatePayment($id, $data) {
        $result = $this->update($id, $data);
        
        if ($result) {
            logActivity('update', 'payments', $id, "Updated payment: " . formatCurrency($data['amount']) . " - {$data['status']}");
        }
        
        return $result;
    }
    
    /**
     * Delete payment with activity log
     */
    public function deletePayment($id) {
        $payment = $this->getById($id);
        $result = $this->delete($id);
        
        if ($result && $payment) {
            logActivity('delete', 'payments', $id, "Deleted payment: " . formatCurrency($payment['amount']));
        }
        
        return $result;
    }
    
    /**
     * Get all payment methods
     */
    public function getAllPaymentMethods() {
        $sql = "SELECT DISTINCT payment_method FROM {$this->table} WHERE payment_method IS NOT NULL ORDER BY payment_method ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Made with Bob
