<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="index.php" class="d-flex gap-2">
            <input type="hidden" name="page" value="clients">
            <input type="text" name="search" class="form-control" placeholder="Search clients..." 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if (isset($_GET['search'])): ?>
                <a href="index.php?page=clients" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Clear
                </a>
            <?php endif; ?>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=clients&action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Client
        </a>
    </div>
</div>

<div class="table-card">
    <div class="card-header">
        <h5><i class="bi bi-people me-2"></i>All Clients</h5>
        <span class="badge bg-primary"><?php echo count($clients); ?> Total</span>
    </div>
    
    <?php if (empty($clients)): ?>
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <?php if (isset($_GET['search'])): ?>
                No clients found matching your search.
            <?php else: ?>
                No clients found. <a href="index.php?page=clients&action=create">Add your first client</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Owner Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Domains</th>
                        <th>Hosting</th>
                        <th>Total Paid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($client['company_name'] ?? 'N/A'); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($client['name']); ?></td>
                            <td>
                                <?php if (!empty($client['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($client['email']); ?>">
                                        <?php echo htmlspecialchars($client['email']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($client['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $client['total_domains'] ?? 0; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $client['total_hosting'] ?? 0; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo formatCurrency($client['total_paid'] ?? 0); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo getStatusBadge($client['status']); ?>">
                                    <?php echo ucfirst($client['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=clients&action=view&id=<?php echo $client['id']; ?>" 
                                       class="btn btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?page=clients&action=edit&id=<?php echo $client['id']; ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" title="Delete"
                                            onclick="deleteClient(<?php echo $client['id']; ?>, '<?php echo htmlspecialchars($client['name']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="card-footer">
                <nav>
                    <ul class="pagination pagination-sm mb-0 justify-content-center">
                        <?php if ($pagination['has_prev']): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=clients&p=<?php echo $pagination['current_page'] - 1; ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=clients&p=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=clients&p=<?php echo $pagination['current_page'] + 1; ?>">
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" action="index.php?page=clients&action=delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<script>
function deleteClient(id, name) {
    if (confirm('Are you sure you want to delete client "' + name + '"?\n\nThis will also delete all associated domains, hosting, and payments!')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob
