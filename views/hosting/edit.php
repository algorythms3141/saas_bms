<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-pencil me-2"></i>Edit Hosting</h5>
                <a href="index.php?page=clients&action=view&id=<?php echo $hosting['client_id']; ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Client
                </a>
            </div>
            
            <div class="p-4">
                <form method="POST" action="index.php?page=hosting&action=update" id="hostingForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="id" value="<?php echo $hosting['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required onchange="loadClientDomains(this.value)">
                            <option value="">Select Client</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>" 
                                        <?php echo ($hosting['client_id'] == $client['id']) ? 'selected' : ''; ?>>
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
                                    <option value="<?php echo $domain['id']; ?>"
                                            <?php echo ($hosting['domain_id'] == $domain['id']) ? 'selected' : ''; ?>>
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
                                   value="<?php echo htmlspecialchars($hosting['server_name']); ?>"
                                   placeholder="Server-US-01" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="plan_name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="plan_name" name="plan_name" 
                                   value="<?php echo htmlspecialchars($hosting['plan_name']); ?>"
                                   placeholder="Basic, Premium, Enterprise" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo $hosting['start_date']; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                                   value="<?php echo $hosting['expiry_date']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cost" class="form-label">Cost (<?php echo CURRENCY_SYMBOL; ?>)</label>
                            <input type="number" class="form-control" id="cost" name="cost" 
                                   step="0.01" min="0" value="<?php echo $hosting['cost']; ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="disk_space" class="form-label">Disk Space</label>
                            <input type="text" class="form-control" id="disk_space" name="disk_space" 
                                   value="<?php echo htmlspecialchars($hosting['disk_space'] ?? ''); ?>"
                                   placeholder="10GB, Unlimited">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="bandwidth" class="form-label">Bandwidth</label>
                            <input type="text" class="form-control" id="bandwidth" name="bandwidth" 
                                   value="<?php echo htmlspecialchars($hosting['bandwidth'] ?? ''); ?>"
                                   placeholder="100GB, Unlimited">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?php echo ($hosting['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="expired" <?php echo ($hosting['status'] == 'expired') ? 'selected' : ''; ?>>Expired</option>
                                <option value="suspended" <?php echo ($hosting['status'] == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" value="1"
                                       <?php echo $hosting['auto_renew'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="auto_renew">
                                    Auto Renew
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($hosting['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Hosting
                        </button>
                        <a href="index.php?page=clients&action=view&id=<?php echo $hosting['client_id']; ?>" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                        <button type="button" class="btn btn-danger ms-auto" 
                                onclick="deleteHosting(<?php echo $hosting['id']; ?>, '<?php echo htmlspecialchars($hosting['server_name']); ?>')">
                            <i class="bi bi-trash"></i> Delete Hosting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" action="index.php?page=hosting&action=delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<script>
function loadClientDomains(clientId) {
    // This would ideally be an AJAX call, but for simplicity we'll reload the page
    if (clientId) {
        window.location.href = 'index.php?page=hosting&action=edit&id=<?php echo $hosting['id']; ?>&client_id=' + clientId;
    }
}

function deleteHosting(id, name) {
    if (confirm('Are you sure you want to delete hosting "' + name + '"?\n\nThis action cannot be undone!')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob