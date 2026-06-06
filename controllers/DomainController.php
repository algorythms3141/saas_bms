<?php
/**
 * Domain Controller
 * 
 * Handles domain management operations
 */

require_once __DIR__ . '/../models/Domain.php';
require_once __DIR__ . '/../models/Client.php';

class DomainController {
    private $domainModel;
    private $clientModel;
    
    public function __construct() {
        $this->domainModel = new Domain();
        $this->clientModel = new Client();
    }
    
    /**
     * List all domains
     */
    public function index() {
        $domains = $this->domainModel->getAllWithClient();
        
        $pageTitle = 'Domains';
        $page = 'domains';
        
        require_once __DIR__ . '/../views/domains/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
        
        // Get all active clients for dropdown
        $clients = $this->clientModel->getActiveClients();
        
        // If client_id is provided, get client details
        $selectedClient = null;
        if ($clientId) {
            $selectedClient = $this->clientModel->getById($clientId);
        }
        
        $pageTitle = 'Add New Domain';
        $page = 'domains';
        
        require_once __DIR__ . '/../views/domains/create.php';
    }
    
    /**
     * Store new domain
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=domains');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=domains&action=create');
        }
        
        // Validate input
        $errors = [];
        
        $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $domain_name = sanitize($_POST['domain_name'] ?? '');
        $provider = sanitize($_POST['provider'] ?? '');
        $purchase_date = sanitize($_POST['purchase_date'] ?? '');
        $expiry_date = sanitize($_POST['expiry_date'] ?? '');
        $cost = isset($_POST['cost']) ? (float)$_POST['cost'] : 0;
        $auto_renew = isset($_POST['auto_renew']) ? 1 : 0;
        $status = sanitize($_POST['status'] ?? 'active');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (!$client_id) {
            $errors[] = 'Please select a client';
        }
        
        if (empty($domain_name)) {
            $errors[] = 'Domain name is required';
        }
        
        if (empty($provider)) {
            $errors[] = 'Provider is required';
        }
        
        if (empty($purchase_date)) {
            $errors[] = 'Purchase date is required';
        }
        
        if (empty($expiry_date)) {
            $errors[] = 'Expiry date is required';
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=domains&action=create&client_id=' . $client_id);
        }
        
        // Create domain
        $data = [
            'client_id' => $client_id,
            'domain_name' => $domain_name,
            'provider' => $provider,
            'purchase_date' => $purchase_date,
            'expiry_date' => $expiry_date,
            'cost' => $cost,
            'auto_renew' => $auto_renew,
            'status' => $status,
            'notes' => $notes
        ];
        
        $domainId = $this->domainModel->createDomain($data);
        
        if ($domainId) {
            setFlashMessage('success', 'Domain created successfully');
            redirect('/index.php?page=clients&action=view&id=' . $client_id);
        } else {
            setFlashMessage('danger', 'Failed to create domain');
            redirect('/index.php?page=domains&action=create&client_id=' . $client_id);
        }
    }
    
    /**
     * Show edit form
     */
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid domain ID');
            redirect('/index.php?page=domains');
        }
        
        $domain = $this->domainModel->getById($id);
        
        if (!$domain) {
            setFlashMessage('danger', 'Domain not found');
            redirect('/index.php?page=domains');
        }
        
        // Get all active clients for dropdown
        $clients = $this->clientModel->getActiveClients();
        
        $pageTitle = 'Edit Domain';
        $page = 'domains';
        
        require_once __DIR__ . '/../views/domains/edit.php';
    }
    
    /**
     * Update domain
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=domains');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid domain ID');
            redirect('/index.php?page=domains');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=domains&action=edit&id=' . $id);
        }
        
        // Validate input
        $errors = [];
        
        $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $domain_name = sanitize($_POST['domain_name'] ?? '');
        $provider = sanitize($_POST['provider'] ?? '');
        $purchase_date = sanitize($_POST['purchase_date'] ?? '');
        $expiry_date = sanitize($_POST['expiry_date'] ?? '');
        $cost = isset($_POST['cost']) ? (float)$_POST['cost'] : 0;
        $auto_renew = isset($_POST['auto_renew']) ? 1 : 0;
        $status = sanitize($_POST['status'] ?? 'active');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (!$client_id) {
            $errors[] = 'Please select a client';
        }
        
        if (empty($domain_name)) {
            $errors[] = 'Domain name is required';
        }
        
        if (empty($provider)) {
            $errors[] = 'Provider is required';
        }
        
        if (empty($purchase_date)) {
            $errors[] = 'Purchase date is required';
        }
        
        if (empty($expiry_date)) {
            $errors[] = 'Expiry date is required';
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=domains&action=edit&id=' . $id);
        }
        
        // Update domain
        $data = [
            'client_id' => $client_id,
            'domain_name' => $domain_name,
            'provider' => $provider,
            'purchase_date' => $purchase_date,
            'expiry_date' => $expiry_date,
            'cost' => $cost,
            'auto_renew' => $auto_renew,
            'status' => $status,
            'notes' => $notes
        ];
        
        $result = $this->domainModel->updateDomain($id, $data);
        
        if ($result) {
            setFlashMessage('success', 'Domain updated successfully');
            redirect('/index.php?page=clients&action=view&id=' . $client_id);
        } else {
            setFlashMessage('danger', 'Failed to update domain');
            redirect('/index.php?page=domains&action=edit&id=' . $id);
        }
    }
    
    /**
     * Delete domain
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=domains');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid domain ID');
            redirect('/index.php?page=domains');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=domains');
        }
        
        $domain = $this->domainModel->getById($id);
        $result = $this->domainModel->deleteDomain($id);
        
        if ($result) {
            setFlashMessage('success', 'Domain deleted successfully');
            if ($domain) {
                redirect('/index.php?page=clients&action=view&id=' . $domain['client_id']);
            }
        } else {
            setFlashMessage('danger', 'Failed to delete domain');
        }
        
        redirect('/index.php?page=domains');
    }
}

// Made with Bob