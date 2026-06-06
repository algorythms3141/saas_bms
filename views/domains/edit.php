<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-pencil me-2"></i>Edit Domain</h5>
                <a href="index.php?page=clients&action=view&id=<?php echo $domain['client_id']; ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Client
                </a>
            </div>
            
            <div class="p-4">
                <form method="POST" action="index.php?page=domains&action=update">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="id" value="<?php echo $domain['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required>
                            <option value="">Select Client</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>" 
                                        <?php echo ($domain['client_id'] == $client['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['company_name'] ?? $client['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="domain_name" name="domain_name" 
                                   value="<?php echo htmlspecialchars($domain['domain_name']); ?>"
                                   placeholder="example.com" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="provider" class="form-label">Provider <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="provider" name="provider" 
                                   value="<?php echo htmlspecialchars($domain['provider']); ?>"
                                   placeholder="GoDaddy, Namecheap, etc." required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date" 
                                   value="<?php echo $domain['purchase_date']; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                                   value="<?php echo $domain['expiry_date']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cost" class="form-label">Cost (<?php echo CURRENCY_SYMBOL; ?>)</label>
                            <input type="number" class="form-control" id="cost" name="cost" 
                                   step="0.01" min="0" value="<?php echo $domain['cost']; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?php echo ($domain['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="expired" <?php echo ($domain['status'] == 'expired') ? 'selected' : ''; ?>>Expired</option>
                                <option value="pending" <?php echo ($domain['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" value="1"
                                   <?php echo $domain['auto_renew'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="auto_renew">
                                Auto Renew
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($domain['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Domain
                        </button>
                        <a href="index.php?page=clients&action=view&id=<?php echo $domain['client_id']; ?>" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Cancel
                        </a>
                        <button type="button" class="btn btn-danger ms-auto" 
                                onclick="deleteDomain(<?php echo $domain['id']; ?>, '<?php echo htmlspecialchars($domain['domain_name']); ?>')">
                            <i class="bi bi-trash"></i> Delete Domain
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" action="index.php?page=domains&action=delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<script>
function deleteDomain(id, name) {
    if (confirm('Are you sure you want to delete domain "' + name + '"?\n\nThis action cannot be undone!')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob