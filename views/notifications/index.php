<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="btn-group" role="group">
            <a href="index.php?page=notifications" class="btn btn-sm <?php echo (!isset($_GET['filter']) || $_GET['filter'] == 'all') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                All
            </a>
            <a href="index.php?page=notifications&filter=unread" class="btn btn-sm <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'unread') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                Unread
            </a>
            <a href="index.php?page=notifications&filter=urgent" class="btn btn-sm <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'urgent') ? 'btn-danger' : 'btn-outline-danger'; ?>">
                Urgent
            </a>
            <a href="index.php?page=notifications&filter=high" class="btn btn-sm <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'high') ? 'btn-warning' : 'btn-outline-warning'; ?>">
                High Priority
            </a>
            <a href="index.php?page=notifications&filter=domain" class="btn btn-sm <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'domain') ? 'btn-info' : 'btn-outline-info'; ?>">
                <i class="bi bi-globe"></i> Domains
            </a>
            <a href="index.php?page=notifications&filter=hosting" class="btn btn-sm <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'hosting') ? 'btn-info' : 'btn-outline-info'; ?>">
                <i class="bi bi-server"></i> Hosting
            </a>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-success" onclick="generateExpiry()">
                <i class="bi bi-arrow-clockwise"></i> Generate Expiry Alerts
            </button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="markAllAsRead()">
                <i class="bi bi-check-all"></i> Mark All Read
            </button>
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteOld()">
                <i class="bi bi-trash"></i> Delete Old
            </button>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="card-header">
        <h5><i class="bi bi-bell me-2"></i>Notifications</h5>
        <span class="badge bg-primary"><?php echo count($notifications); ?> Total</span>
    </div>
    
    <?php if (empty($notifications)): ?>
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            No notifications found. All clear!
        </div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($notifications as $notification): ?>
                <?php
                // Determine priority badge
                $priorityBadge = 'secondary';
                $priorityIcon = 'bi-info-circle';
                switch ($notification['priority']) {
                    case 'urgent':
                        $priorityBadge = 'danger';
                        $priorityIcon = 'bi-exclamation-triangle-fill';
                        break;
                    case 'high':
                        $priorityBadge = 'warning';
                        $priorityIcon = 'bi-exclamation-circle-fill';
                        break;
                    case 'medium':
                        $priorityBadge = 'info';
                        $priorityIcon = 'bi-info-circle-fill';
                        break;
                    case 'low':
                        $priorityBadge = 'secondary';
                        $priorityIcon = 'bi-info-circle';
                        break;
                }
                
                // Determine type icon
                $typeIcon = 'bi-bell';
                switch ($notification['type']) {
                    case 'domain_expiry':
                        $typeIcon = 'bi-globe';
                        break;
                    case 'hosting_expiry':
                        $typeIcon = 'bi-server';
                        break;
                    case 'payment':
                        $typeIcon = 'bi-credit-card';
                        break;
                }
                
                // Check if read
                $isRead = $notification['is_read'];
                $bgClass = $isRead ? '' : 'bg-light';
                ?>
                <div class="list-group-item <?php echo $bgClass; ?>">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-<?php echo $priorityBadge; ?> me-2">
                                    <i class="bi <?php echo $priorityIcon; ?>"></i> <?php echo ucfirst($notification['priority']); ?>
                                </span>
                                <span class="badge bg-secondary me-2">
                                    <i class="bi <?php echo $typeIcon; ?>"></i> <?php echo ucfirst(str_replace('_', ' ', $notification['type'])); ?>
                                </span>
                                <?php if (!$isRead): ?>
                                    <span class="badge bg-primary">New</span>
                                <?php endif; ?>
                            </div>
                            
                            <h6 class="mb-1 <?php echo !$isRead ? 'fw-bold' : ''; ?>">
                                <?php echo htmlspecialchars($notification['title']); ?>
                            </h6>
                            
                            <p class="mb-2 text-muted">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </p>
                            
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> <?php echo timeAgo($notification['created_at']); ?>
                            </small>
                        </div>
                        
                        <div class="btn-group btn-group-sm ms-3">
                            <?php if ($notification['related_type'] && $notification['related_id']): ?>
                                <?php
                                $viewUrl = '';
                                if ($notification['related_type'] == 'domain') {
                                    $viewUrl = 'index.php?page=domains&action=edit&id=' . $notification['related_id'];
                                } elseif ($notification['related_type'] == 'hosting') {
                                    $viewUrl = 'index.php?page=hosting&action=edit&id=' . $notification['related_id'];
                                }
                                ?>
                                <?php if ($viewUrl): ?>
                                    <a href="<?php echo $viewUrl; ?>" class="btn btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if (!$isRead): ?>
                                <button type="button" class="btn btn-success" title="Mark as Read"
                                        onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                    <i class="bi bi-check"></i>
                                </button>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-danger" title="Delete"
                                    onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Mark as Read Form (Hidden) -->
<form id="markAsReadForm" method="POST" action="index.php?page=notifications&action=markAsRead" style="display: none;">
    <input type="hidden" name="id" id="markAsReadId">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<!-- Mark All as Read Form (Hidden) -->
<form id="markAllAsReadForm" method="POST" action="index.php?page=notifications&action=markAllAsRead" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" action="index.php?page=notifications&action=delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<!-- Generate Expiry Form (Hidden) -->
<form id="generateExpiryForm" method="POST" action="index.php?page=notifications&action=generateExpiry" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<!-- Delete Old Form (Hidden) -->
<form id="deleteOldForm" method="POST" action="index.php?page=notifications&action=deleteOld" style="display: none;">
    <input type="hidden" name="days" value="30">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
</form>

<script>
function markAsRead(id) {
    document.getElementById('markAsReadId').value = id;
    document.getElementById('markAsReadForm').submit();
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        document.getElementById('markAllAsReadForm').submit();
    }
}

function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function generateExpiry() {
    if (confirm('Generate expiry notifications for domains and hosting expiring in the next 30 days?')) {
        document.getElementById('generateExpiryForm').submit();
    }
}

function deleteOld() {
    if (confirm('Delete all read notifications older than 30 days?')) {
        document.getElementById('deleteOldForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

// Made with Bob