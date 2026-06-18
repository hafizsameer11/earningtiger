<?php
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_proof'])) {
        if (empty($_FILES['image']['name'])) {
            flash('error', 'Please select an image to upload.');
        } else {
            $upload = uploadImage($_FILES['image'], PROOFS_UPLOAD_PATH, 'proof_');
            if (!$upload['success']) {
                flash('error', $upload['error']);
            } else {
                $stmt = $db->prepare('INSERT INTO payment_proofs (title, image_file, sort_order, is_active) VALUES (?, ?, ?, ?)');
                $stmt->execute([
                    trim($_POST['title'] ?? ''),
                    $upload['filename'],
                    (int) ($_POST['sort_order'] ?? 0),
                    isset($_POST['is_active']) ? 1 : 0,
                ]);
                flash('success', 'Payment proof added.');
            }
        }
        header('Location: proofs.php');
        exit;
    }

    if (isset($_POST['update_proof'])) {
        $id = (int) $_POST['id'];
        $proof = getPaymentProofById($id);
        if ($proof) {
            $imageFile = $proof['image_file'];
            if (!empty($_FILES['image']['name'])) {
                $upload = uploadImage($_FILES['image'], PROOFS_UPLOAD_PATH, 'proof_');
                if (!$upload['success']) {
                    flash('error', $upload['error']);
                    header('Location: proofs.php?edit=' . $id);
                    exit;
                }
                if (file_exists(PROOFS_UPLOAD_PATH . '/' . $proof['image_file'])) {
                    unlink(PROOFS_UPLOAD_PATH . '/' . $proof['image_file']);
                }
                $imageFile = $upload['filename'];
            }
            $stmt = $db->prepare('UPDATE payment_proofs SET title=?, image_file=?, sort_order=?, is_active=? WHERE id=?');
            $stmt->execute([
                trim($_POST['title'] ?? ''),
                $imageFile,
                (int) ($_POST['sort_order'] ?? 0),
                isset($_POST['is_active']) ? 1 : 0,
                $id,
            ]);
            flash('success', 'Payment proof updated.');
        }
        header('Location: proofs.php');
        exit;
    }

    if (isset($_POST['delete_id'])) {
        $id = (int) $_POST['delete_id'];
        $proof = getPaymentProofById($id);
        if ($proof && file_exists(PROOFS_UPLOAD_PATH . '/' . $proof['image_file'])) {
            unlink(PROOFS_UPLOAD_PATH . '/' . $proof['image_file']);
        }
        $db->prepare('DELETE FROM payment_proofs WHERE id = ?')->execute([$id]);
        flash('success', 'Payment proof deleted.');
        header('Location: proofs.php');
        exit;
    }

    if (isset($_POST['save_section'])) {
        setSetting('proofs_section_title', trim($_POST['proofs_section_title'] ?? ''));
        setSetting('proofs_section_subtitle', trim($_POST['proofs_section_subtitle'] ?? ''));
        flash('success', 'Slider section text saved.');
        header('Location: proofs.php');
        exit;
    }
}

$proofs = getAllPaymentProofs();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
$editProof = $editId ? getPaymentProofById($editId) : null;

$adminTitle = 'Payment Proofs';
require_once 'includes/header.php';
?>

<div class="admin-card">
    <h2>Slider Section Text</h2>
    <form method="post" style="max-width:600px">
        <input type="hidden" name="save_section" value="1">
        <div class="form-group">
            <label>Section Title</label>
            <input type="text" name="proofs_section_title" value="<?= e(getSetting('proofs_section_title', 'Payment Proofs')) ?>">
        </div>
        <div class="form-group">
            <label>Section Subtitle</label>
            <input type="text" name="proofs_section_subtitle" value="<?= e(getSetting('proofs_section_subtitle')) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save Section Text</button>
    </form>
</div>

<div class="admin-card">
    <h2><?= $editProof ? 'Edit Payment Proof' : 'Add Payment Proof' ?></h2>
    <form method="post" enctype="multipart/form-data" style="max-width:600px">
        <?php if ($editProof): ?>
            <input type="hidden" name="id" value="<?= $editProof['id'] ?>">
            <input type="hidden" name="update_proof" value="1">
        <?php else: ?>
            <input type="hidden" name="add_proof" value="1">
        <?php endif; ?>
        <div class="form-group">
            <label>Title (optional)</label>
            <input type="text" name="title" value="<?= e($editProof['title'] ?? '') ?>" placeholder="e.g. JazzCash Payment">
        </div>
        <div class="form-group">
            <label>Image <?= $editProof ? '(leave empty to keep current)' : '*' ?></label>
            <input type="file" name="image" accept="image/*" <?= $editProof ? '' : 'required' ?>>
            <?php if ($editProof): ?>
                <img src="../<?= e(proofImageUrl($editProof['image_file'])) ?>" class="receipt-thumb" style="margin-top:10px;max-width:200px;max-height:140px" alt="">
            <?php endif; ?>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?= e($editProof['sort_order'] ?? '0') ?>">
            </div>
            <div class="form-group">
                <label class="radio-label" style="margin-top:28px">
                    <input type="checkbox" name="is_active" <?= ($editProof['is_active'] ?? 1) ? 'checked' : '' ?>> Show on website
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $editProof ? 'Update' : 'Add' ?> Proof</button>
        <?php if ($editProof): ?>
            <a href="proofs.php" class="btn" style="margin-left:8px;background:#e2e8f0;color:#334155">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-card">
    <h2>All Payment Proofs (<?= count($proofs) ?>)</h2>
    <?php if (empty($proofs)): ?>
        <p class="text-muted">No payment proof images yet. Upload screenshots to show in the homepage slider.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Active</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proofs as $p): ?>
                    <tr>
                        <td><img src="../<?= e(proofImageUrl($p['image_file'])) ?>" class="receipt-thumb" alt=""></td>
                        <td><?= e($p['title'] ?: '—') ?></td>
                        <td><?= $p['is_active'] ? '✓ Yes' : '✗ No' ?></td>
                        <td><?= $p['sort_order'] ?></td>
                        <td>
                            <a href="proofs.php?edit=<?= $p['id'] ?>">Edit</a>
                            <form method="post" style="display:inline" data-confirm="Delete this payment proof?">
                                <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;margin-left:8px">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
