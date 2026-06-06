<?php
/**
 * Client Controller
 * 
 * Handles client management operations
 */

require_once __DIR__ . '/../models/Client.php';

class ClientController {
    private $clientModel;
    
    public function __construct() {
        $this->clientModel = new Client();
    }
    
    /**
     * List all clients
     */
    public function index() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
        
        if ($search) {
            $clients = $this->clientModel->searchClients($search);
            $pagination = null;
        } else {
            $result = $this->clientModel->paginate($page, RECORDS_PER_PAGE, null, [], 'name ASC');
            $clients = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // Get clients with statistics
        $clients = $this->clientModel->getAllWithStats('c.name ASC');
        
        $pageTitle = 'Clients';
        $page = 'clients';
        
        require_once __DIR__ . '/../views/clients/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $pageTitle = 'Add New Client';
        $page = 'clients';
        
        require_once __DIR__ . '/../views/clients/create.php';
    }
    
    /**
     * Store new client
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=clients');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=clients&action=create');
        }
        
        // Validate input
        $errors = [];
        
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $company_name = sanitize($_POST['company_name'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');
        
        if (empty($company_name)) {
            $errors[] = 'Company name is required';
        }
        
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } elseif ($this->clientModel->emailExists($email)) {
                $errors[] = 'Email already exists';
            }
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=clients&action=create');
        }
        
        // Create client
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company_name' => $company_name,
            'address' => $address,
            'notes' => $notes,
            'status' => $status
        ];
        
        $clientId = $this->clientModel->createClient($data);
        
        if ($clientId) {
            setFlashMessage('success', 'Client created successfully');
            redirect('/index.php?page=clients&action=view&id=' . $clientId);
        } else {
            setFlashMessage('danger', 'Failed to create client');
            redirect('/index.php?page=clients&action=create');
        }
    }
    
    /**
     * Show edit form
     */
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid client ID');
            redirect('/index.php?page=clients');
        }
        
        $client = $this->clientModel->getById($id);
        
        if (!$client) {
            setFlashMessage('danger', 'Client not found');
            redirect('/index.php?page=clients');
        }
        
        $pageTitle = 'Edit Client';
        $page = 'clients';
        
        require_once __DIR__ . '/../views/clients/edit.php';
    }
    
    /**
     * Update client
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=clients');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid client ID');
            redirect('/index.php?page=clients');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=clients&action=edit&id=' . $id);
        }
        
        // Validate input
        $errors = [];
        
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $company_name = sanitize($_POST['company_name'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');
        
        if (empty($company_name)) {
            $errors[] = 'Company name is required';
        }
        
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } elseif ($this->clientModel->emailExists($email, $id)) {
                $errors[] = 'Email already exists';
            }
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=clients&action=edit&id=' . $id);
        }
        
        // Update client
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company_name' => $company_name,
            'address' => $address,
            'notes' => $notes,
            'status' => $status
        ];
        
        $result = $this->clientModel->updateClient($id, $data);
        
        if ($result) {
            setFlashMessage('success', 'Client updated successfully');
            redirect('/index.php?page=clients&action=view&id=' . $id);
        } else {
            setFlashMessage('danger', 'Failed to update client');
            redirect('/index.php?page=clients&action=edit&id=' . $id);
        }
    }
    
    /**
     * View client details
     */
    public function view() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid client ID');
            redirect('/index.php?page=clients');
        }
        
        $client = $this->clientModel->getClientDetails($id);
        
        if (!$client) {
            setFlashMessage('danger', 'Client not found');
            redirect('/index.php?page=clients');
        }
        
        // Get client's domains
        require_once __DIR__ . '/../models/Domain.php';
        $domainModel = new Domain();
        $domains = $domainModel->getByClient($id);
        
        // Get client's hosting
        require_once __DIR__ . '/../models/Hosting.php';
        $hostingModel = new Hosting();
        $hosting = $hostingModel->getByClient($id);
        
        // Get client's payments
        require_once __DIR__ . '/../models/Payment.php';
        $paymentModel = new Payment();
        $payments = $paymentModel->getByClient($id);
        
        $pageTitle = 'Client Details';
        $page = 'clients';
        
        require_once __DIR__ . '/../views/clients/view.php';
    }
    
    /**
     * Delete client
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=clients');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid client ID');
            redirect('/index.php?page=clients');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=clients');
        }
        
        $result = $this->clientModel->deleteClient($id);
        
        if ($result) {
            setFlashMessage('success', 'Client deleted successfully');
        } else {
            setFlashMessage('danger', 'Failed to delete client');
        }
        
        redirect('/index.php?page=clients');
    }
}

// Made with Bob
