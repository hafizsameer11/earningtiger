<?php
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();

$db = getDB();

$filter = $_GET['filter'] ?? 'all';
if (!array_key_exists($filter, getDateFilterOptions())) {
    $filter = 'all';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int) $_POST['id'];
    $status = $_POST['status'] ?? '';
    $notes = trim($_POST['admin_notes'] ?? '');
    if (in_array($status, ['pending', 'submitted', 'approved', 'rejected'], true)) {
        $stmt = $db->prepare('UPDATE registrations SET status = ?, admin_notes = ? WHERE id = ?');
        $stmt->execute([$status, $notes, $id]);
        flash('success', 'Registration updated.');
        header('Location: users.php?view=' . $id . ($filter !== 'all' ? '&filter=' . $filter : ''));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    $reg = getRegistrationById($id);
    if ($reg && $reg['receipt_file'] && file_exists(UPLOAD_PATH . '/' . $reg['receipt_file'])) {
        unlink(UPLOAD_PATH . '/' . $reg['receipt_file']);
    }
    $db->prepare('DELETE FROM registrations WHERE id = ?')->execute([$id]);
    flash('success', 'Registration deleted.');
    header('Location: users.php' . ($filter !== 'all' ? '?filter=' . $filter : ''));
    exit;
}

$viewId = isset($_GET['view']) ? (int) $_GET['view'] : null;
$viewReg = $viewId ? getRegistrationById($viewId) : null;

if ($viewReg && $viewReg['payment_method_id']) {
    $pm = $db->prepare('SELECT name FROM payment_methods WHERE id = ?');
    $pm->execute([$viewReg['payment_method_id']]);
    $viewReg['payment_name'] = $pm->fetchColumn();
}

[$where, $params] = buildDateFilterClause($filter, 'created_at');
$countStmt = $db->prepare("SELECT COUNT(*) FROM registrations WHERE $where");
$countStmt->execute($params);
$filteredCount = (int) $countStmt->fetchColumn();

$listStmt = $db->prepare("SELECT r.*, p.name as payment_name FROM registrations r LEFT JOIN payment_methods p ON r.payment_method_id = p.id WHERE $where ORDER BY r.created_at DESC");
$listStmt->execute($params);
$registrations = $listStmt->fetchAll();

$listUrl = 'users.php' . ($filter !== 'all' ? '?filter=' . $filter : '');

$adminTitle = 'Registrations';
require_once 'includes/header.php';
?>

<?php if ($viewReg): ?>
<div class="admin-card">
    <h2>Registration #<?= str_pad($viewReg['id'], 5, '0', STR_PAD_LEFT) ?></h2>
    <table class="admin-table" style="max-width:700px">
        <tr><th>Name</th><td><?= e($viewReg['full_name']) ?></td></tr>
        <tr><th>Email</th><td><?= e($viewReg['email']) ?></td></tr>
        <tr><th>Phone</th><td><?= e($viewReg['phone']) ?></td></tr>
        <tr><th>City</th><td><?= e($viewReg['city'] ?: '—') ?></td></tr>
        <tr><th>Gender</th><td><?= e(ucfirst($viewReg['gender'])) ?></td></tr>
        <tr><th>Shift</th><td><?= e($viewReg['shift_type']) ?></td></tr>
        <tr><th>Payment Method</th><td><?= e($viewReg['payment_name'] ?? '—') ?></td></tr>
        <tr><th>Transaction ID</th><td><?= e($viewReg['transaction_id'] ?: '—') ?></td></tr>
        <tr><th>Amount</th><td><?= e($viewReg['amount'] ? 'PKR ' . $viewReg['amount'] : '—') ?></td></tr>
        <tr><th>Status</th><td><span class="status-badge status-<?= e($viewReg['status']) ?>"><?= e(ucfirst($viewReg['status'])) ?></span></td></tr>
        <tr><th>Submitted</th><td><?= formatDate($viewReg['created_at']) ?></td></tr>
        <?php if ($viewReg['receipt_file']): ?>
        <tr><th>Receipt</th><td>
            <a href="receipt.php?f=<?= e(urlencode($viewReg['receipt_file'])) ?>" target="_blank">
                <img src="receipt.php?f=<?= e(urlencode($viewReg['receipt_file'])) ?>" class="receipt-thumb" alt="Receipt">
            </a>
        </td></tr>
        <?php endif; ?>
    </table>

    <form method="post" style="margin-top:24px;max-width:500px">
        <input type="hidden" name="id" value="<?= $viewReg['id'] ?>">
        <input type="hidden" name="update_status" value="1">
        <div class="form-group">
            <label>Update Status</label>
            <select name="status">
                <?php foreach (['pending','submitted','approved','rejected'] as $st): ?>
                    <option value="<?= $st ?>" <?= $viewReg['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Admin Notes</label>
            <textarea name="admin_notes" rows="3"><?= e($viewReg['admin_notes'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="<?= e($listUrl) ?>" class="btn" style="margin-left:8px;background:#e2e8f0;color:#334155">Back to List</a>
    </form>

    <form method="post" style="margin-top:16px" data-confirm="Delete this registration permanently?">
        <input type="hidden" name="delete_id" value="<?= $viewReg['id'] ?>">
        <button type="submit" class="btn btn-danger btn-sm">Delete Registration</button>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <h2>Registrations (<?= $filteredCount ?>)</h2>
    <?php renderDateFilter($filter, 'users.php'); ?>

    <?php if (empty($registrations)): ?>
        <p class="text-muted">No registrations for this period.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Shift</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $r): ?>
                    <tr>
                        <td>#<?= str_pad($r['id'], 5, '0', STR_PAD_LEFT) ?></td>
                        <td><?= e($r['full_name']) ?></td>
                        <td><?= e($r['email']) ?></td>
                        <td><?= e($r['phone']) ?></td>
                        <td><?= e(ucfirst($r['gender'] ?? '—')) ?></td>
                        <td><?= e($r['shift_type']) ?></td>
                        <td><?= e($r['payment_name'] ?? '—') ?></td>
                        <td><span class="status-badge status-<?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                        <td><?= formatDate($r['created_at']) ?></td>
                        <td><a href="users.php?view=<?= $r['id'] ?><?= $filter !== 'all' ? '&filter=' . $filter : '' ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
