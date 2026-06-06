<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="index.php" class="d-flex gap-2">
            <input type="hidden" name="page" value="domains">
            <input type="text" name="search" class="form-control" placeholder="Search domains..." 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if (isset($_GET['search'])): ?>
                <a href="index.php?page=domains" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Clear
                </a>
            <?php endif; ?>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=domains&action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Domain
        </a>
    </div>
</div>

<div class="table-card">
    <div class="card-header">
        <h5><i class="bi bi-globe me-2"></i>All Domains</h5>
        <span class="badge bg-primary"><?php echo count($domains); ?> Total</span>
    </div>
    
    <?php if (empty($domains)): ?>
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <?php if (isset($_GET['search'])): ?>
                No domains found matching your search.
            <?php else: ?>
                No domains found. <a href="index.php?page=domains&action=create">Add your first domain</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Domain Name</th>
                        <th>Client</th>
                        <th>Provider</th>
                        <th>Purchase Date</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>Cost</th>
                        <th>Auto Renew</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($domains as $domain): ?>
                        <?php
                        $daysLeft = $domain['days_left'];
                        $expiryClass = '';
                        if ($daysLeft < 0) {
                            $expiryClass = 'text-danger fw-bold';
                        } elseif ($daysLeft <= 7) {
                            $expiryClass = 'text-danger';
                        } elseif ($daysLeft <= 30) {
                            $expiryClass = 'text-warning';
                        }
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($domain['domain_name']); ?></strong>
                            </td>
                            <td>
                                <a href="index.php?page=clients&action=view&id=<?php echo $domain['client_id']; ?>">
                                    <?php echo htmlspecialchars($domain['client_name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($domain['provider']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($domain['purchase_date'])); ?></td>
                            <td class="<?php echo $expiryClass; ?>">
                                <?php echo date('M d, Y', strtotime($domain['expiry_date'])); ?>
                            </td>
                            <td class="<?php echo $expiryClass; ?>">
                                <?php 
                                if ($daysLeft < 0) {
                                    echo 'Expired ' . abs($daysLeft) . ' days ago';
                                } elseif ($daysLeft == 0) {
                                    echo 'Expires today!';
                                } else {
                                    echo $daysLeft . ' days';
                                }
                                ?>
                            </td>
                            <td>
                                <strong><?php echo formatCurrency($domain['cost']); ?></strong>
                            </td>
                            <td>
                                <?php if ($domain['auto_renew']): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Yes
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle"></i> No
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusBadge = 'secondary';
                                switch ($domain['status']) {
                                    case 'active':
                                        $statusBadge = 'success';
                                        break;
                                    case 'expired':
                                        $statusBadge = 'danger';
                                        break;
                                    case 'pending':
                                        $statusBadge = 'warning';
                                        break;
                                }
                                ?>
                                <span class="badge bg-<?php echo $statusBadge; ?>">
                                    <?php echo ucfirst($domain['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=clients&action=view&id=<?php echo $domain['client_id']; ?>" 
                                       class="btn btn-info" title="View Client">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?page=domains&action=edit&id=<?php echo $domain['id']; ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" title="Delete"
                                            onclick="deleteDomain(<?php echo $domain['id']; ?>, '<?php echo htmlspecialchars($domain['domain_name']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
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