<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-pencil me-2"></i>Edit Client</h5>
                <a href="index.php?page=clients" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
            
            <div class="p-4">
                <form method="POST" action="index.php?page=clients&action=update">
                    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   value="<?php echo htmlspecialchars($client['company_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Owner Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?php echo htmlspecialchars($client['name']); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($client['email']); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($client['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($client['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?php echo $client['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $client['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Client
                        </button>
                        <a href="index.php?page=clients&action=view&id=<?php echo $client['id']; ?>" class="btn btn-secondary">
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
