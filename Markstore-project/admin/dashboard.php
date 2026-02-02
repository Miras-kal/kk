<?php
include '../includes/config.php';

// Әкімшілерге рұқсат тексеру (қалауыңа қарай осы жерге тексеріс қойсаң болады)

$page_title = "Басқару тақтасы";
 include '../includes/admin_header.php';

// Статистиканы алу
$stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
$total_products = $stmt->fetch()['total_products'];

$stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
$total_orders = $stmt->fetch()['total_orders'];

$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch()['total_users'];

$stmt = $pdo->query("SELECT SUM(total) as revenue FROM orders WHERE status = 'completed'");
$revenue = $stmt->fetch()['revenue'] ?? 0;

// Соңғы тапсырыстар
$stmt = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>

<div class="admin-content">
    <h1>Басқару тақтасы</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #4e73df;">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3>Тауарлар</h3>
                <p><?= $total_products ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #1cc88a;">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3>Тапсырыстар</h3>
                <p><?= $total_orders ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #36b9cc;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Пайдаланушылар</h3>
                <p><?= $total_users ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #f6c23e;">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>Табыс</h3>
                <p><?= number_format($revenue, 0, ',', ' ') ?> ₸</p>
            </div>
        </div>
    </div>

    <div class="dashboard-sections">
        <div class="recent-orders">
            <h2>Соңғы тапсырыстар</h2>

            <?php if (empty($recent_orders)): ?>
                <p>Тапсырыстар жоқ</p>
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
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                                <td><?= number_format($order['total'], 0, ',', ' ') ?> ₸</td>
                                <td>
                                    <span class="status-badge <?= htmlspecialchars($order['status']) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="orders/detalis.php?id=<?= $order['id'] ?>" class="btn btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
