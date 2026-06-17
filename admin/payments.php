<?php
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_method'])) {
        $stmt = $db->prepare('INSERT INTO payment_methods (name, account_title, account_number, instructions, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['account_title']),
            trim($_POST['account_number']),
            trim($_POST['instructions'] ?? ''),
            isset($_POST['is_active']) ? 1 : 0,
            (int) ($_POST['sort_order'] ?? 0),
        ]);
        flash('success', 'Payment method added.');
        header('Location: payments.php');
        exit;
    }

    if (isset($_POST['update_method'])) {
        $stmt = $db->prepare('UPDATE payment_methods SET name=?, account_title=?, account_number=?, instructions=?, is_active=?, sort_order=? WHERE id=?');
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['account_title']),
            trim($_POST['account_number']),
            trim($_POST['instructions'] ?? ''),
            isset($_POST['is_active']) ? 1 : 0,
            (int) ($_POST['sort_order'] ?? 0),
            (int) $_POST['id'],
        ]);
        flash('success', 'Payment method updated.');
        header('Location: payments.php');
        exit;
    }

    if (isset($_POST['delete_id'])) {
        $db->prepare('DELETE FROM payment_methods WHERE id = ?')->execute([(int) $_POST['delete_id']]);
        flash('success', 'Payment method deleted.');
        header('Location: payments.php');
        exit;
    }
}

$methods = getAllPaymentMethods();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editMethod = null;
if ($editId) {
    foreach ($methods as $m) {
        if ((int) $m['id'] === $editId) {
            $editMethod = $m;
            break;
        }
    }
}

$adminTitle = 'Payment Methods';
require_once 'includes/header.php';
?>

<div class="admin-card">
    <h2><?= $editMethod ? 'Edit Payment Method' : 'Add Payment Method' ?></h2>
    <form method="post" style="max-width:600px">
        <?php if ($editMethod): ?>
            <input type="hidden" name="id" value="<?= $editMethod['id'] ?>">
            <input type="hidden" name="update_method" value="1">
        <?php else: ?>
            <input type="hidden" name="add_method" value="1">
        <?php endif; ?>
        <div class="form-group">
            <label>Method Name (e.g. JazzCash, EasyPaisa)</label>
            <input type="text" name="name" required value="<?= e($editMethod['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Account Title</label>
            <input type="text" name="account_title" required value="<?= e($editMethod['account_title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Account Number</label>
            <input type="text" name="account_number" required value="<?= e($editMethod['account_number'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Instructions for Users</label>
            <textarea name="instructions" rows="3"><?= e($editMethod['instructions'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?= e($editMethod['sort_order'] ?? '0') ?>">
            </div>
            <div class="form-group">
                <label class="radio-label" style="margin-top:28px">
                    <input type="checkbox" name="is_active" <?= ($editMethod['is_active'] ?? 1) ? 'checked' : '' ?>> Active
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editMethod ? 'Update' : 'Add' ?> Method</button>
        <?php if ($editMethod): ?>
            <a href="payments.php" class="btn" style="margin-left:8px;background:#e2e8f0;color:#334155">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-card">
    <h2>Current Payment Methods</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Account Title</th>
                <th>Account Number</th>
                <th>Active</th>
                <th>Order</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($methods as $m): ?>
                <tr>
                    <td><strong><?= e($m['name']) ?></strong></td>
                    <td><?= e($m['account_title']) ?></td>
                    <td><code><?= e($m['account_number']) ?></code></td>
                    <td><?= $m['is_active'] ? '✓ Yes' : '✗ No' ?></td>
                    <td><?= $m['sort_order'] ?></td>
                    <td>
                        <a href="payments.php?edit=<?= $m['id'] ?>">Edit</a>
                        <form method="post" style="display:inline" data-confirm="Delete this payment method?">
                            <input type="hidden" name="delete_id" value="<?= $m['id'] ?>">
                            <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;margin-left:8px">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
