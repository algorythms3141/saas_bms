<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total Clients -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon" style="background-color: rgba(102, 126, 234, 0.1); color: #667eea;">
                <i class="bi bi-people"></i>
            </div>
            <h3><?php echo $stats['total_clients']; ?></h3>
            <p>Total Clients</p>
            <small class="text-success">
                <i class="bi bi-check-circle"></i> <?php echo $stats['active_clients']; ?> Active
            </small>
        </div>
    </div>
    
    <!-- Active Domains -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">
                <i class="bi bi-globe"></i>
            </div>
            <h3><?php echo $stats['active_domains']; ?></h3>
            <p>Active Domains</p>
            <small class="text-danger">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $stats['expiring_domains_7']; ?> Expiring Soon
            </small>
        </div>
    </div>
    
    <!-- Active Hosting -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon" style="background-color: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                <i class="bi bi-server"></i>
            </div>
            <h3><?php echo $stats['active_hosting']; ?></h3>
            <p>Active Hosting</p>
            <small class="text-danger">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $stats['expiring_hosting_7']; ?> Expiring Soon
            </small>
        </div>
    </div>
    
    <!-- Monthly Revenue -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon" style="background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <h3><?php echo formatCurrency($stats['monthly_revenue']); ?></h3>
            <p>Monthly Revenue</p>
            <small class="text-warning">
                <i class="bi bi-clock"></i> <?php echo formatCurrency($stats['pending_amount']); ?> Pending
            </small>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Monthly Revenue Chart -->
    <div class="col-md-8">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-bar-chart me-2"></i>Monthly Revenue</h5>
            </div>
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>
    
    <!-- Service Distribution Chart -->
    <div class="col-md-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart me-2"></i>Revenue by Service</h5>
            </div>
            <canvas id="serviceChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Expiring Services Row -->
<div class="row g-4 mb-4">
    <!-- Expiring Domains -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-globe me-2"></i>Expiring Domains (Next 30 Days)</h5>
                <a href="index.php?page=domains" class="btn btn-sm btn-primary">View All</a>
            </div>
            
            <?php if (empty($expiringDomains)): ?>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>No domains expiring in the next 30 days!
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Domain</th>
                                <th>Client</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($expiringDomains, 0, 5) as $domain): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($domain['domain_name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($domain['client_name']); ?></td>
                                    <td><?php echo formatDate($domain['expiry_date']); ?></td>
                                    <td>
                                        <strong class="text-<?php echo getAlertClass($domain['days_left']); ?>">
                                            <?php echo $domain['days_left']; ?> days
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getAlertClass($domain['days_left']); ?>">
                                            <?php 
                                            if ($domain['days_left'] <= 7) echo 'Urgent';
                                            elseif ($domain['days_left'] <= 15) echo 'Warning';
                                            else echo 'Info';
                                            ?>
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
    
    <!-- Expiring Hosting -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-server me-2"></i>Expiring Hosting (Next 30 Days)</h5>
                <a href="index.php?page=hosting" class="btn btn-sm btn-primary">View All</a>
            </div>
            
            <?php if (empty($expiringHosting)): ?>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>No hosting expiring in the next 30 days!
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Server</th>
                                <th>Client</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($expiringHosting, 0, 5) as $hosting): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($hosting['server_name']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($hosting['plan_name']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($hosting['client_name']); ?></td>
                                    <td><?php echo formatDate($hosting['expiry_date']); ?></td>
                                    <td>
                                        <strong class="text-<?php echo getAlertClass($hosting['days_left']); ?>">
                                            <?php echo $hosting['days_left']; ?> days
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getAlertClass($hosting['days_left']); ?>">
                                            <?php 
                                            if ($hosting['days_left'] <= 7) echo 'Urgent';
                                            elseif ($hosting['days_left'] <= 15) echo 'Warning';
                                            else echo 'Info';
                                            ?>
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

<!-- Recent Activity Row -->
<div class="row g-4">
    <!-- Recent Payments -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-credit-card me-2"></i>Recent Payments</h5>
                <a href="index.php?page=payments" class="btn btn-sm btn-primary">View All</a>
            </div>
            
            <?php if (empty($recentPayments)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>No recent payments.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['client_name']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst($payment['service_type']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo formatCurrency($payment['amount']); ?></strong></td>
                                    <td><?php echo formatDate($payment['payment_date']); ?></td>
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
    
    <!-- Recent Notifications -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="bi bi-bell me-2"></i>Recent Notifications</h5>
                <a href="index.php?page=notifications" class="btn btn-sm btn-primary">View All</a>
            </div>
            
            <?php if (empty($recentNotifications)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>No recent notifications.
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentNotifications as $notification): ?>
                        <div class="list-group-item <?php echo $notification['is_read'] ? '' : 'bg-light'; ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="bi bi-<?php 
                                        echo $notification['type'] === 'domain_expiry' ? 'globe' : 
                                             ($notification['type'] === 'hosting_expiry' ? 'server' : 'bell'); 
                                    ?> me-2"></i>
                                    <?php echo htmlspecialchars($notification['title']); ?>
                                </h6>
                                <small class="text-muted">
                                    <?php echo date('M d, H:i', strtotime($notification['created_at'])); ?>
                                </small>
                            </div>
                            <p class="mb-1 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <span class="badge bg-<?php 
                                echo $notification['priority'] === 'urgent' ? 'danger' : 
                                     ($notification['priority'] === 'high' ? 'warning' : 'info'); 
                            ?>">
                                <?php echo ucfirst($notification['priority']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Monthly Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: [
            <?php 
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $revenueData = array_fill(0, 12, 0);
            foreach ($monthlyRevenue as $data) {
                $revenueData[$data['month'] - 1] = $data['total_amount'];
            }
            foreach ($months as $month) {
                echo "'$month',";
            }
            ?>
        ],
        datasets: [{
            label: 'Revenue',
            data: [<?php echo implode(',', $revenueData); ?>],
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '<?php echo CURRENCY_SYMBOL; ?>' + value;
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return '<?php echo CURRENCY_SYMBOL; ?>' + context.parsed.y.toFixed(2);
                    }
                }
            }
        }
    }
});

// Service Distribution Chart
const serviceCtx = document.getElementById('serviceChart').getContext('2d');
const serviceChart = new Chart(serviceCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php 
            foreach ($revenueByService as $service) {
                echo "'" . ucfirst($service['service_type']) . "',";
            }
            ?>
        ],
        datasets: [{
            data: [
                <?php 
                foreach ($revenueByService as $service) {
                    echo $service['total_amount'] . ',';
                }
                ?>
            ],
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(118, 75, 162, 0.8)',
                'rgba(255, 193, 7, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': <?php echo CURRENCY_SYMBOL; ?>' + context.parsed.toFixed(2);
                    }
                }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob
