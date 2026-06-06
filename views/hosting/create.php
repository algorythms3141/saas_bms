<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-server me-2"></i>Add New Hosting</h5>
                <a href="<?php echo isset($selectedClient) ? 'index.php?page=clients&action=view&id=' . $selectedClient['id'] : 'index.php?page=hosting'; ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            
            <div class="p-4">
                <form method="POST" action="index.php?page=hosting&action=store" id="hostingForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required onchange="loadClientDomains(this.value)">
                            <option value="">Select Client</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>" 
                                        <?php echo (isset($selectedClient) && $selectedClient['id'] == $client['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['company_name'] ?? $client['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="domain_id" class="form-label">Associated Domain (Optional)</label>
                        <select class="form-select" id="domain_id" name="domain_id">
                            <option value="">None</option>
                            <?php if (!empty($clientDomains)): ?>
                                <?php foreach ($clientDomains as $domain): ?>
                                    <option value="<?php echo $domain['id']; ?>">
                                        <?php echo htmlspecialchars($domain['domain_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="server_name" class="form-label">Server Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="server_name" name="server_name" 
                                   placeholder="Server-US-01" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="plan_name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="plan_name" name="plan_name" 
                                   placeholder="Basic, Premium, Enterprise" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cost" class="form-label">Cost (<?php echo CURRENCY_SYMBOL; ?>)</label>
                            <input type="number" class="form-control" id="cost" name="cost" 
                                   step="0.01" min="0" value="0">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="disk_space" class="form-label">Disk Space</label>
                            <input type="text" class="form-control" id="disk_space" name="disk_space" 
                                   placeholder="10GB, Unlimited">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="bandwidth" class="form-label">Bandwidth</label>
                            <input type="text" class="form-control" id="bandwidth" name="bandwidth" 
                                   placeholder="100GB, Unlimited">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="expired">Expired</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" value="1">
                                <label class="form-check-label" for="auto_renew">
                                    Auto Renew
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Hosting
                        </button>
                        <a href="<?php echo isset($selectedClient) ? 'index.php?page=clients&action=view&id=' . $selectedClient['id'] : 'index.php?page=hosting'; ?>" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadClientDomains(clientId) {
    // This would ideally be an AJAX call, but for simplicity we'll reload the page
    if (clientId) {
        window.location.href = 'index.php?page=hosting&action=create&client_id=' + clientId;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob