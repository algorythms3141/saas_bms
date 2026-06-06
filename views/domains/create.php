<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-globe me-2"></i>Add New Domain</h5>
                <a href="<?php echo isset($selectedClient) ? 'index.php?page=clients&action=view&id=' . $selectedClient['id'] : 'index.php?page=domains'; ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            
            <div class="p-4">
                <form method="POST" action="index.php?page=domains&action=store">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required>
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
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="domain_name" name="domain_name" 
                                   placeholder="example.com" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="provider" class="form-label">Provider <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="provider" name="provider" 
                                   placeholder="GoDaddy, Namecheap, etc." required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cost" class="form-label">Cost (<?php echo CURRENCY_SYMBOL; ?>)</label>
                            <input type="number" class="form-control" id="cost" name="cost" 
                                   step="0.01" min="0" value="0">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="expired">Expired</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" value="1">
                            <label class="form-check-label" for="auto_renew">
                                Auto Renew
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Domain
                        </button>
                        <a href="<?php echo isset($selectedClient) ? 'index.php?page=clients&action=view&id=' . $selectedClient['id'] : 'index.php?page=domains'; ?>" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob