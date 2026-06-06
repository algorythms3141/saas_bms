<?php
/**
 * Payment Controller
 * 
 * Handles payment management operations
 */

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Domain.php';
require_once __DIR__ . '/../models/Hosting.php';

class PaymentController {
    private $paymentModel;
    private $clientModel;
    private $domainModel;
    private $hostingModel;
    
    public function __construct() {
        $this->paymentModel = new Payment();
        $this->clientModel = new Client();
        $this->domainModel = new Domain();
        $this->hostingModel = new Hosting();
    }
    
    /**
     * List all payments
     */
    public function index() {
        $payments = $this->paymentModel->getAllWithDetails();
        
        $pageTitle = 'Payments';
        $page = 'payments';
        
        require_once __DIR__ . '/../views/payments/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
        $serviceType = isset($_GET['service_type']) ? sanitize($_GET['service_type']) : '';
        $serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
        
        // Get all active clients for dropdown
        $clients = $this->clientModel->getActiveClients();
        
        // If client_id is provided, get client details and their services
        $selectedClient = null;
        $clientDomains = [];
        $clientHosting = [];
        if ($clientId) {
            $selectedClient = $this->clientModel->getById($clientId);
            $clientDomains = $this->domainModel->getByClient($clientId);
            $clientHosting = $this->hostingModel->getByClient($clientId);
        }
        
        $pageTitle = 'Add New Payment';
        $page = 'payments';
        
        require_once __DIR__ . '/../views/payments/create.php';
    }
    
    /**
     * Store new payment
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=payments');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=payments&action=create');
        }
        
        // Validate input
        $errors = [];
        
        $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $service_type = sanitize($_POST['service_type'] ?? '');
        $service_id = isset($_POST['service_id']) && !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
        $payment_date = sanitize($_POST['payment_date'] ?? '');
        $payment_method = sanitize($_POST['payment_method'] ?? '');
        $transaction_id = sanitize($_POST['transaction_id'] ?? '');
        $status = sanitize($_POST['status'] ?? 'pending');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (!$client_id) {
            $errors[] = 'Please select a client';
        }
        
        if (empty($service_type)) {
            $errors[] = 'Service type is required';
        }
        
        if ($amount <= 0) {
            $errors[] = 'Amount must be greater than 0';
        }
        
        if (empty($payment_date)) {
            $errors[] = 'Payment date is required';
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=payments&action=create&client_id=' . $client_id);
        }
        
        // Create payment
        $data = [
            'client_id' => $client_id,
            'service_type' => $service_type,
            'service_id' => $service_id,
            'amount' => $amount,
            'payment_date' => $payment_date,
            'payment_method' => $payment_method,
            'transaction_id' => $transaction_id,
            'status' => $status,
            'notes' => $notes
        ];
        
        $paymentId = $this->paymentModel->createPayment($data);
        
        if ($paymentId) {
            setFlashMessage('success', 'Payment created successfully');
            redirect('/index.php?page=clients&action=view&id=' . $client_id);
        } else {
            setFlashMessage('danger', 'Failed to create payment');
            redirect('/index.php?page=payments&action=create&client_id=' . $client_id);
        }
    }
    
    /**
     * Show edit form
     */
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid payment ID');
            redirect('/index.php?page=payments');
        }
        
        $payment = $this->paymentModel->getById($id);
        
        if (!$payment) {
            setFlashMessage('danger', 'Payment not found');
            redirect('/index.php?page=payments');
        }
        
        // Get all active clients for dropdown
        $clients = $this->clientModel->getActiveClients();
        
        // Get services for the selected client
        $clientDomains = $this->domainModel->getByClient($payment['client_id']);
        $clientHosting = $this->hostingModel->getByClient($payment['client_id']);
        
        $pageTitle = 'Edit Payment';
        $page = 'payments';
        
        require_once __DIR__ . '/../views/payments/edit.php';
    }
    
    /**
     * Update payment
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=payments');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid payment ID');
            redirect('/index.php?page=payments');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=payments&action=edit&id=' . $id);
        }
        
        // Validate input
        $errors = [];
        
        $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;
        $service_type = sanitize($_POST['service_type'] ?? '');
        $service_id = isset($_POST['service_id']) && !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
        $payment_date = sanitize($_POST['payment_date'] ?? '');
        $payment_method = sanitize($_POST['payment_method'] ?? '');
        $transaction_id = sanitize($_POST['transaction_id'] ?? '');
        $status = sanitize($_POST['status'] ?? 'pending');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if (!$client_id) {
            $errors[] = 'Please select a client';
        }
        
        if (empty($service_type)) {
            $errors[] = 'Service type is required';
        }
        
        if ($amount <= 0) {
            $errors[] = 'Amount must be greater than 0';
        }
        
        if (empty($payment_date)) {
            $errors[] = 'Payment date is required';
        }
        
        if (!empty($errors)) {
            setFlashMessage('danger', implode('<br>', $errors));
            redirect('/index.php?page=payments&action=edit&id=' . $id);
        }
        
        // Update payment
        $data = [
            'client_id' => $client_id,
            'service_type' => $service_type,
            'service_id' => $service_id,
            'amount' => $amount,
            'payment_date' => $payment_date,
            'payment_method' => $payment_method,
            'transaction_id' => $transaction_id,
            'status' => $status,
            'notes' => $notes
        ];
        
        $result = $this->paymentModel->updatePayment($id, $data);
        
        if ($result) {
            setFlashMessage('success', 'Payment updated successfully');
            redirect('/index.php?page=clients&action=view&id=' . $client_id);
        } else {
            setFlashMessage('danger', 'Failed to update payment');
            redirect('/index.php?page=payments&action=edit&id=' . $id);
        }
    }
    
    /**
     * Delete payment
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=payments');
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        if (!$id) {
            setFlashMessage('danger', 'Invalid payment ID');
            redirect('/index.php?page=payments');
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('danger', 'Invalid security token');
            redirect('/index.php?page=payments');
        }
        
        $payment = $this->paymentModel->getById($id);
        $result = $this->paymentModel->deletePayment($id);
        
        if ($result) {
            setFlashMessage('success', 'Payment deleted successfully');
            if ($payment) {
                redirect('/index.php?page=clients&action=view&id=' . $payment['client_id']);
            }
        } else {
            setFlashMessage('danger', 'Failed to delete payment');
        }
        
        redirect('/index.php?page=payments');
    }
}

// Made with Bob