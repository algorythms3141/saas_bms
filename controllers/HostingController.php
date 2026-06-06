<?php
/**
 * Hosting Controller
 * 
 * Handles hosting management operations
 */

require_once __DIR__ . '/../models/Hosting.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Domain.php';

class HostingController {
    private $hostingModel;
    private $clientModel;
    private $domainModel;
    
    public function __construct() {
        $this->hostingModel = new Hosting();
        $this->clientModel = new Client();
        $this->domainModel = new Domain();
    }
    
    /**
     * List all hosting
     */
    public function index() {
        $hosting = $this->hostingModel->getAllWithDetails();
        
        $pageTitle = 'Hosting';
        $page = 'hosting';
        
        require_once __DIR__ . '/../views/hosting/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
        
        // Get all active clients for dropdown
        $clients = $this->clientModel->getActiveClients();
        
        // If client_id is provided, get client details and their domains
        $selectedClient = null;
        $clientDomains = [];
        if ($clientId) {
            $selectedClient = $this->clientModel->getById($clientId);
            $clientDomains = $this->domainModel->getByClient($clientId);
        }
        
        $pageTitle = 'Add New Hosting';
        $page = 'hosting';
        
        require_once __DIR__ . '/../views/hosting/create.php';
    }
    
    /**
     * Store new hosting
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=hosting');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=hosting&action=create');
        }
        
        // Validate input
        $errors = [];
        
        $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $domain_id = isset($_POST['domain_id']) && !empty($_POST['domain_id']) ? (int)$_POST['domain_id'] : null;
        $server_name = sanitize($_POST['server_name'] ?? '');
        $plan_name = sanitize($_POST['plan_name'] ?? '');
        $start_date = sanitize($_POST['start_date'] ?? '');
        $expiry_date = sanitize($_POST['expiry_date'] ?? '');
        $cost = isset($_POST['cost']) ? (float)$_POST['cost'] : 0;
        $disk_space = sanitize($_POST['disk_space'] ?? '');
        $bandwidth = sanitize($_POST['bandwidth'] ?? '');
        $auto_renew = isset($_POST['auto_renew']) ? 1 : 0;
        $status = sanitize($_POST['status'] ?? 'active');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (!$client_id) {
            $errors[] = 'Please select a client';
        }
        
        if (empty($server_name)) {
            $errors[] = 'Server name is required';
        }
        
        if (empty($plan_name)) {
            $errors[] = 'Plan name is required';
        }
        
        if (empty($start_date)) {
            $errors[] = 'Start date is required';
        }
        
        if (empty($expiry_date)) {
            $errors[] = 'Expiry date is required';
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=hosting&action=create&client_id=' . $client_id);
        }
        
        // Create hosting
        $data = [
            'client_id' => $client_id,
            'domain_id' => $domain_id,
            'server_name' => $server_name,
            'plan_name' => $plan_name,
            'start_date' => $start_date,
            'expiry_date' => $expiry_date,
            'cost' => $cost,
            'disk_space' => $disk_space,
            'bandwidth' => $bandwidth,
            'auto_renew' => $auto_renew,
            'status' => $status,
            'notes' => $notes
        ];
        
        $hostingId = $this->hostingModel->createHosting($data);
        
        if ($hostingId) {
            setFlashMessage('success', 'Hosting created successfully');
            redirect('/index.php?page=clients&action=view&id=' . $client_id);
        } else {
            setFlashMessage('danger', 'Failed to create hosting');
            redirect('/index.php?page=hosting&action=create&client_id=' . $client_id);
        }
    }
    
    /**
     * Show edit form
     */
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid hosting ID');
            redirect('/index.php?page=hosting');
        }
        
        $hosting = $this->hostingModel->getById($id);
        
        if (!$hosting) {
            setFlashMessage('danger', 'Hosting not found');
            redirect('/index.php?page=hosting');
        }
        
        // Get all active clients for dropdown
        $clients = $this->clientModel->getActiveClients();
        
        // Get domains for the selected client
        $clientDomains = $this->domainModel->getByClient($hosting['client_id']);
        
        $pageTitle = 'Edit Hosting';
        $page = 'hosting';
        
        require_once __DIR__ . '/../views/hosting/edit.php';
    }
    
    /**
     * Update hosting
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=hosting');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid hosting ID');
            redirect('/index.php?page=hosting');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=hosting&action=edit&id=' . $id);
        }
        
        // Validate input
        $errors = [];
        
        $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $domain_id = isset($_POST['domain_id']) && !empty($_POST['domain_id']) ? (int)$_POST['domain_id'] : null;
        $server_name = sanitize($_POST['server_name'] ?? '');
        $plan_name = sanitize($_POST['plan_name'] ?? '');
        $start_date = sanitize($_POST['start_date'] ?? '');
        $expiry_date = sanitize($_POST['expiry_date'] ?? '');
        $cost = isset($_POST['cost']) ? (float)$_POST['cost'] : 0;
        $disk_space = sanitize($_POST['disk_space'] ?? '');
        $bandwidth = sanitize($_POST['bandwidth'] ?? '');
        $auto_renew = isset($_POST['auto_renew']) ? 1 : 0;
        $status = sanitize($_POST['status'] ?? 'active');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (!$client_id) {
            $errors[] = 'Please select a client';
        }
        
        if (empty($server_name)) {
            $errors[] = 'Server name is required';
        }
        
        if (empty($plan_name)) {
            $errors[] = 'Plan name is required';
        }
        
        if (empty($start_date)) {
            $errors[] = 'Start date is required';
        }
        
        if (empty($expiry_date)) {
            $errors[] = 'Expiry date is required';
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=hosting&action=edit&id=' . $id);
        }
        
        // Update hosting
        $data = [
            'client_id' => $client_id,
            'domain_id' => $domain_id,
            'server_name' => $server_name,
            'plan_name' => $plan_name,
            'start_date' => $start_date,
            'expiry_date' => $expiry_date,
            'cost' => $cost,
            'disk_space' => $disk_space,
            'bandwidth' => $bandwidth,
            'auto_renew' => $auto_renew,
            'status' => $status,
            'notes' => $notes
        ];
        
        $result = $this->hostingModel->updateHosting($id, $data);
        
        if ($result) {
            setFlashMessage('success', 'Hosting updated successfully');
            redirect('/index.php?page=clients&action=view&id=' . $client_id);
        } else {
            setFlashMessage('danger', 'Failed to update hosting');
            redirect('/index.php?page=hosting&action=edit&id=' . $id);
        }
    }
    
    /**
     * Delete hosting
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=hosting');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid hosting ID');
            redirect('/index.php?page=hosting');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=hosting');
        }
        
        $hosting = $this->hostingModel->getById($id);
        $result = $this->hostingModel->deleteHosting($id);
        
        if ($result) {
            setFlashMessage('success', 'Hosting deleted successfully');
            if ($hosting) {
                redirect('/index.php?page=clients&action=view&id=' . $hosting['client_id']);
            }
        } else {
            setFlashMessage('danger', 'Failed to delete hosting');
        }
        
        redirect('/index.php?page=hosting');
    }
}

// Made with Bob