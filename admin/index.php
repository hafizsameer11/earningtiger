<?php
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();

$db = getDB();
$total = $db->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
$pending = $db->query("SELECT COUNT(*) FROM registrations WHERE status IN ('pending','submitted')")->fetchColumn();
$approved = $db->query("SELECT COUNT(*) FROM registrations WHERE status = 'approved'")->fetchColumn();
$rejected = $db->query("SELECT COUNT(*) FROM registrations WHERE status = 'rejected'")->fetchColumn();

$todayRegs = $db->prepare("SELECT COUNT(*) FROM registrations WHERE date(created_at) = ?");
$todayRegs->execute([date('Y-m-d')]);
$todayRegs = (int) $todayRegs->fetchColumn();
$visitStats = getVisitStats('today');
$visitStats7 = getVisitStats('7days');

$recent = $db->query('SELECT r.*, p.name as payment_name FROM registrations r LEFT JOIN payment_methods p ON r.payment_method_id = p.id ORDER BY r.created_at DESC LIMIT 10')->fetchAll();

$adminTitle = 'Dashboard';
require_once 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card"><h3><?= $total ?></h3><p>Total Registrations</p></div>
    <div class="stat-card"><h3><?= $pending ?></h3><p>Pending Review</p></div>
    <div class="stat-card"><h3><?= $todayRegs ?></h3><p>Registrations Today</p></div>
    <div class="stat-card"><h3><?= $approved ?></h3><p>Approved</p></div>
</div>

<div class="stats-grid">
    <div class="stat-card"><h3><?= $visitStats['total'] ?></h3><p>Visits Today</p></div>
    <div class="stat-card"><h3><?= $visitStats['unique'] ?></h3><p>Unique Visitors Today</p></div>
    <div class="stat-card"><h3><?= $visitStats7['total'] ?></h3><p>Visits (7 Days)</p></div>
    <div class="stat-card"><h3><?= $rejected ?></h3><p>Rejected</p></div>
</div>

<p style="margin-bottom:24px"><a href="visitors.php">View full visitor analytics →</a> · <a href="users.php?filter=today">Today's registrations →</a></p>

<div class="admin-card">
    <h2>Recent Registrations</h2>
    <?php if (empty($recent)): ?>
        <p class="text-muted">No registrations yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Shift</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $r): ?>
                    <tr>
                        <td>#<?= str_pad($r['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td><?= e($r['full_name']) ?></td>
                        <td><?= e($r['phone']) ?></td>
                        <td><?= e($r['shift_type']) ?></td>
                        <td><?= e($r['payment_name'] ?? '—') ?></td>
                        <td><span class="status-badge status-<?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                        <td><?= formatDate($r['created_at']) ?></td>
                        <td><a href="users.php?view=<?= $r['id'] ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
