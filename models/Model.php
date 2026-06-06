<?php
/**
 * Base Model Class
 * 
 * All models extend this base class for common database operations
 */

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    /**
     * Get all records
     */
    public function getAll($orderBy = null, $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table}";
        
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
     * Get record by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get records by column value
     */
    public function getBy($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get single record by column value
     */
    public function getOneBy($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }
    
    /**
     * Insert new record
     */
    public function insert($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update record
     */
    public function update($id, $data) {
        $columns = array_keys($data);
        $setClause = implode(' = ?, ', $columns) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Count total records
     */
    public function count($where = null, $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Check if record exists
     */
    public function exists($column, $value, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = ?";
        $params = [$value];
        
        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] > 0;
    }
    
    /**
     * Execute custom query
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Execute custom query and return single row
     */
    public function queryOne($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->rollBack();
    }
    
    /**
     * Search records
     */
    public function search($columns, $searchTerm, $limit = null, $offset = null) {
        $conditions = [];
        $params = [];
        
        foreach ($columns as $column) {
            $conditions[] = "{$column} LIKE ?";
            $params[] = "%{$searchTerm}%";
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' OR ', $conditions);
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get records with pagination
     */
    public function paginate($page = 1, $perPage = RECORDS_PER_PAGE, $where = null, $params = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(),
            'pagination' => getPagination(
                $this->count($where, $params),
                $page,
                $perPage
            )
        ];
    }
}

// Made with Bob
