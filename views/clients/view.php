<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4><i class="bi bi-person me-2"></i><?php echo htmlspecialchars($client['name']); ?></h4>
            <div class="btn-group">
                <a href="index.php?page=clients" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <a href="index.php?page=clients&action=edit&id=<?php echo $client['id']; ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Client Info Card -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle me-2"></i>Client Information</h5>
                <span class="badge bg-<?php echo getStatusBadge($client['status']); ?>">
                    <?php echo ucfirst($client['status']); ?>
                </span>
            </div>
            <div class="p-4">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Full Name:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($client['name']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Email:</strong></div>
                    <div class="col-md-8">
                        <a href="mailto:<?php echo htmlspecialchars($client['email']); ?>">
                            <?php echo htmlspecialchars($client['email']); ?>
                        </a>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Phone:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($client['phone'] ?? 'N/A'); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Company:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($client['company_name'] ?? 'N/A'); ?></div>
                </div>
                <?php if (!empty($client['address'])): ?>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Address:</strong></div>
                    <div class="col-md-8"><?php echo nl2br(htmlspecialchars($client['address'])); ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($client['notes'])): ?>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Notes:</strong></div>
                    <div class="col-md-8"><?php echo nl2br(htmlspecialchars($client['notes'])); ?></div>
                </div>
                <?php endif; ?>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Created:</strong></div>
                    <div class="col-md-8"><?php echo formatDate($client['created_at'], DISPLAY_DATETIME_FORMAT); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-bar-chart me-2"></i>Statistics</h5>
            </div>
            <div class="p-4">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Domains:</span>
                        <span class="badge bg-info"><?php echo $client['total_domains'] ?? 0; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Hosting:</span>
                        <span class="badge bg-info"><?php echo $client['total_hosting'] ?? 0; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Paid:</span>
                        <strong class="text-success"><?php echo formatCurrency($client['total_paid'] ?? 0); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Pending Amount:</span>
                        <strong class="text-warning"><?php echo formatCurrency($client['pending_amount'] ?? 0); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Domains -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-globe me-2"></i>Domains (<?php echo count($domains); ?>)</h5>
                <a href="index.php?page=domains&action=create&client_id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Add Domain
                </a>
            </div>
            <?php if (empty($domains)): ?>
                <div class="p-3">
                    <div class="alert alert-info mb-0">No domains found for this client.</div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Domain Name</th>
                                <th>Provider</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($domains as $domain): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($domain['domain_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($domain['provider']); ?></td>
                                    <td><?php echo formatDate($domain['expiry_date']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getAlertClass($domain['days_left']); ?>">
                                            <?php echo $domain['days_left']; ?> days
                                        </span>
                                    </td>
                                    <td><?php echo formatCurrency($domain['cost']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusBadge($domain['status']); ?>">
                                            <?php echo ucfirst($domain['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hosting -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-server me-2"></i>Hosting (<?php echo count($hosting); ?>)</h5>
                <a href="index.php?page=hosting&action=create&client_id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Add Hosting
                </a>
            </div>
            <?php if (empty($hosting)): ?>
                <div class="p-3">
                    <div class="alert alert-info mb-0">No hosting found for this client.</div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Server</th>
                                <th>Plan</th>
                                <th>Domain</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hosting as $host): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($host['server_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($host['plan_name']); ?></td>
                                    <td><?php echo htmlspecialchars($host['domain_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo formatDate($host['expiry_date']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getAlertClass($host['days_left']); ?>">
                                            <?php echo $host['days_left']; ?> days
                                        </span>
                                    </td>
                                    <td><?php echo formatCurrency($host['cost']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusBadge($host['status']); ?>">
                                            <?php echo ucfirst($host['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Payments -->
<div class="row g-4">
    <div class="col-md-12">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-credit-card me-2"></i>Payments (<?php echo count($payments); ?>)</h5>
                <a href="index.php?page=payments&action=create&client_id=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Add Payment
                </a>
            </div>
            <?php if (empty($payments)): ?>
                <div class="p-3">
                    <div class="alert alert-info mb-0">No payments found for this client.</div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service Type</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo formatDate($payment['payment_date']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst($payment['service_type']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo formatCurrency($payment['amount']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($payment['payment_method'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusBadge($payment['status']); ?>">
                                            <?php echo ucfirst($payment['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob
