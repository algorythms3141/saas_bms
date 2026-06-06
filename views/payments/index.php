<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="index.php" class="d-flex gap-2">
            <input type="hidden" name="page" value="payments">
            <input type="text" name="search" class="form-control" placeholder="Search payments..." 
                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if (isset($_GET['search'])): ?>
                <a href="index.php?page=payments" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Clear
                </a>
            <?php endif; ?>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=payments&action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Payment
        </a>
    </div>
</div>

<div class="table-card">
    <div class="card-header">
        <h5><i class="bi bi-credit-card me-2"></i>All Payments</h5>
        <span class="badge bg-primary"><?php echo count($payments); ?> Total</span>
    </div>
    
    <?php if (empty($payments)): ?>
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <?php if (isset($_GET['search'])): ?>
                No payments found matching your search.
            <?php else: ?>
                No payments found. <a href="index.php?page=payments&action=create">Add your first payment</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Client</th>
                        <th>Service Type</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td>
                                <strong><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo date('h:i A', strtotime($payment['created_at'])); ?></small>
                            </td>
                            <td>
                                <a href="index.php?page=clients&action=view&id=<?php echo $payment['client_id']; ?>">
                                    <?php echo htmlspecialchars($payment['client_name']); ?>
                                </a>
                                <?php if (!empty($payment['client_company'])): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($payment['client_company']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $typeIcon = '';
                                $typeClass = '';
                                switch ($payment['service_type']) {
                                    case 'domain':
                                        $typeIcon = 'bi-globe';
                                        $typeClass = 'info';
                                        break;
                                    case 'hosting':
                                        $typeIcon = 'bi-server';
                                        $typeClass = 'primary';
                                        break;
                                    case 'other':
                                        $typeIcon = 'bi-tag';
                                        $typeClass = 'secondary';
                                        break;
                                }
                                ?>
                                <span class="badge bg-<?php echo $typeClass; ?>">
                                    <i class="bi <?php echo $typeIcon; ?>"></i> <?php echo ucfirst($payment['service_type']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($payment['service_name']) && $payment['service_name'] != 'N/A'): ?>
                                    <small><?php echo htmlspecialchars($payment['service_name']); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong class="text-success"><?php echo formatCurrency($payment['amount']); ?></strong>
                            </td>
                            <td>
                                <?php if (!empty($payment['payment_method'])): ?>
                                    <small><?php echo htmlspecialchars($payment['payment_method']); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($payment['transaction_id'])): ?>
                                    <small><code><?php echo htmlspecialchars($payment['transaction_id']); ?></code></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusBadge = 'secondary';
                                switch ($payment['status']) {
                                    case 'paid':
                                        $statusBadge = 'success';
                                        break;
                                    case 'pending':
                                        $statusBadge = 'warning';
                                        break;
                                    case 'failed':
                                        $statusBadge = 'danger';
                                        break;
                                    case 'refunded':
                                        $statusBadge = 'info';
                                        break;
                                }
                                ?>
                                <span class="badge bg-<?php echo $statusBadge; ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=clients&action=view&id=<?php echo $payment['client_id']; ?>" 
                                       class="btn btn-info" title="View Client">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?page=payments&action=edit&id=<?php echo $payment['id']; ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" title="Delete"
                                            onclick="deletePayment(<?php echo $payment['id']; ?>, '<?php echo formatCurrency($payment['amount']); ?>')">
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
<form id="deleteForm" method="POST" action="index.php?page=payments&action=delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<script>
function deletePayment(id, amount) {
    if (confirm('Are you sure you want to delete payment of ' + amount + '?\n\nThis action cannot be undone!')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob