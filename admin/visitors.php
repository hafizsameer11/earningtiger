<?php
require_once __DIR__ . '/config/config.php';
requireAdmin();

$filter = $_GET['filter'] ?? 'all';
if (!array_key_exists($filter, getDateFilterOptions())) {
    $filter = 'all';
}

$db = getDB();
[$where, $params] = buildDateFilterClause($filter, 'visit_date');
$stats = getVisitStats($filter);
$byCountry = getVisitsByCountry($filter);

$stmt = $db->prepare("SELECT * FROM site_visits WHERE $where ORDER BY created_at DESC LIMIT 500");
$stmt->execute($params);
$visits = $stmt->fetchAll();

$adminTitle = 'Site Visitors';
require_once 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card"><h3><?= $stats['total'] ?></h3><p>Total Visits</p></div>
    <div class="stat-card"><h3><?= $stats['unique'] ?></h3><p>Unique Visitors (by IP)</p></div>
    <div class="stat-card"><h3><?= count($byCountry) ?></h3><p>Countries / Regions</p></div>
    <div class="stat-card"><h3><?= e(getDateFilterOptions()[$filter]) ?></h3><p>Selected Period</p></div>
</div>

<?php renderDateFilter($filter, 'visitors.php'); ?>

<div class="admin-card">
    <h2>Visitors by Location</h2>
    <?php if (empty($byCountry)): ?>
        <p class="text-muted">No visitor data for this period.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Code</th>
                    <th>Visits</th>
                    <th>Unique IPs</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($byCountry as $row): ?>
                    <tr>
                        <td><?= e($row['country'] ?: 'Unknown') ?></td>
                        <td><?= e($row['country_code'] ?: '—') ?></td>
                        <td><?= (int) $row['visits'] ?></td>
                        <td><?= (int) $row['unique_ips'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="admin-card">
    <h2>Visit Log (<?= count($visits) ?>)</h2>
    <?php if (empty($visits)): ?>
        <p class="text-muted">No visits recorded yet. Visitors are tracked once per day per user on the public site.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>IP</th>
                    <th>Location</th>
                    <th>Page</th>
                    <th>Referrer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visits as $v): ?>
                    <tr>
                        <td><?= e($v['visit_date']) ?></td>
                        <td><?= e(date('h:i A', strtotime($v['created_at']))) ?></td>
                        <td><code><?= e($v['ip_address']) ?></code></td>
                        <td>
                            <?= e($v['city'] ?: '—') ?><?= $v['region'] ? ', ' . e($v['region']) : '' ?>
                            <br><small><?= e($v['country'] ?: 'Unknown') ?></small>
                        </td>
                        <td><small><?= e($v['page_url']) ?></small></td>
                        <td><small><?= e($v['referrer'] ? parse_url($v['referrer'], PHP_URL_HOST) ?: $v['referrer'] : 'Direct') ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
