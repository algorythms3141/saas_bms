<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-credit-card me-2"></i>Add New Payment</h5>
                <a href="<?php echo isset($selectedClient) ? 'index.php?page=clients&action=view&id=' . $selectedClient['id'] : 'index.php?page=payments'; ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            
            <div class="p-4">
                <form method="POST" action="index.php?page=payments&action=store" id="paymentForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required onchange="loadClientServices(this.value)">
                            <option value="">Select Client</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>" 
                                        <?php echo (isset($selectedClient) && $selectedClient['id'] == $client['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['company_name'] ?? $client['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="service_type" class="form-label">Service Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="service_type" name="service_type" required onchange="updateServiceOptions()">
                                <option value="">Select Type</option>
                                <option value="domain" <?php echo (isset($_GET['service_type']) && $_GET['service_type'] == 'domain') ? 'selected' : ''; ?>>Domain</option>
                                <option value="hosting" <?php echo (isset($_GET['service_type']) && $_GET['service_type'] == 'hosting') ? 'selected' : ''; ?>>Hosting</option>
                                <option value="other" <?php echo (isset($_GET['service_type']) && $_GET['service_type'] == 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="service_id" class="form-label">Related Service (Optional)</label>
                            <select class="form-select" id="service_id" name="service_id">
                                <option value="">None</option>
                                <optgroup label="Domains" id="domain_options">
                                    <?php if (!empty($clientDomains)): ?>
                                        <?php foreach ($clientDomains as $domain): ?>
                                            <option value="<?php echo $domain['id']; ?>" data-type="domain"
                                                    <?php echo (isset($_GET['service_id']) && $_GET['service_id'] == $domain['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($domain['domain_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </optgroup>
                                <optgroup label="Hosting" id="hosting_options">
                                    <?php if (!empty($clientHosting)): ?>
                                        <?php foreach ($clientHosting as $hosting): ?>
                                            <option value="<?php echo $hosting['id']; ?>" data-type="hosting"
                                                    <?php echo (isset($_GET['service_id']) && $_GET['service_id'] == $hosting['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($hosting['server_name'] . ' - ' . $hosting['plan_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount (<?php echo CURRENCY_SYMBOL; ?>) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0.01" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <input type="text" class="form-control" id="payment_method" name="payment_method" 
                                   placeholder="Bank Transfer, PayPal, Credit Card">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                                   placeholder="Optional">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="paid" selected>Paid</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Payment
                        </button>
                        <a href="<?php echo isset($selectedClient) ? 'index.php?page=clients&action=view&id=' . $selectedClient['id'] : 'index.php?page=payments'; ?>" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadClientServices(clientId) {
    if (clientId) {
        window.location.href = 'index.php?page=payments&action=create&client_id=' + clientId;
    }
}

function updateServiceOptions() {
    const serviceType = document.getElementById('service_type').value;
    const serviceSelect = document.getElementById('service_id');
    const options = serviceSelect.querySelectorAll('option[data-type]');
    
    // Reset selection
    serviceSelect.value = '';
    
    // Show/hide options based on service type
    options.forEach(option => {
        const optionType = option.getAttribute('data-type');
        if (serviceType === 'other' || serviceType === '' || optionType === serviceType) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateServiceOptions();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob