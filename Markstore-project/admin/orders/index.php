<?php
include '../../includes/config.php';

if (!is_admin()) {
    redirect('/');
}

$page_title = "Тапсырыстар";
include '../../includes/admin_header.php';

// Сүзгілер
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
$params = [];

if ($status) {
    $sql .= " AND o.status = ?";
    $params[] = $status;
}

if ($search) {
    $sql .= " AND (u.username LIKE ? OR o.id = ?)";
    $params[] = "%$search%";
    $params[] = $search;
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>Тапсырыстар</h1>
        
        <div class="filters">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="ID немесе пайдаланушы..." value="<?= htmlspecialchars($search) ?>">
                <select name="status">
                    <option value="">Барлығы</option>
                    <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Күтуде</option>
                    <option value="processing" <?= $status == 'processing' ? 'selected' : '' ?>>Өңделуде</option>
                    <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Аяқталған</option>
                    <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Бас тартылған</option>
                </select>
                <button type="submit" class="btn btn-primary">Сүзгілеу</button>
            </form>
        </div>
    </div>
    
    <?php if (empty($orders)): ?>
        <p>Тапсырыстар табылмады</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пайдаланушы</th>
                    <th>Құны</th>
                    <th>Статус</th>
                    <th>Күні</th>
                    <th>Әрекет</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['username'] ?></td>
                        <td><?= number_format($order['total'], 0, ',', ' ') ?> ₸</td>
                        <td>
                            <span class="status-badge <?= $order['status'] ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="/admin/orders/detalis.php?id=<?= $order['id'] ?>" class="btn btn-view">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/products/edit.php?id=<?= $order['id'] ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../../includes/admin_footer.php'; ?>