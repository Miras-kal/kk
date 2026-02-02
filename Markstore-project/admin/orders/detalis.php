<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../includes/config.php';

// Тек әкімшілерге рұқсат ету
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Тапсырыс ақпараты";
include '../includes/admin_header.php';

// Тапсырыс ID-сын алу
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Тапсырыс туралы негізгі ақпаратты алу
$stmt = $pdo->prepare("
    SELECT o.*, u.username, u.email, u.phone 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo '<div class="alert alert-danger">Тапсырыс табылмады</div>';
    include '../includes/admin_footer.php';
    exit;
}

// Тапсырыс элементтерін алу
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<div class="admin-content">
    <h1>Тапсырыс ақпараты #<?= $order['id'] ?></h1>
    
    <div class="order-details-grid">
        <div class="order-info-card">
            <h2>Негізгі ақпарат</h2>
            <div class="info-row">
                <span class="info-label">Тапсырыс ID:</span>
                <span class="info-value">#<?= $order['id'] ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Статус:</span>
                <span class="info-value status-badge <?= $order['status'] ?>"><?= $order['status'] ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Құны:</span>
                <span class="info-value"><?= number_format($order['total'], 0, ',', ' ') ?> ₸</span>
            </div>
            <div class="info-row">
                <span class="info-label">Жасалған күні:</span>
                <span class="info-value"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
            </div>
        </div>
        
        <div class="customer-info-card">
            <h2>Тұтынушы ақпараты</h2>
            <div class="info-row">
                <span class="info-label">Аты:</span>
                <span class="info-value"><?= htmlspecialchars($order['username']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?= htmlspecialchars($order['email']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Телефон:</span>
                <span class="info-value"><?= htmlspecialchars($order['phone']) ?></span>
            </div>
        </div>
        
        <div class="shipping-info-card">
            <h2>Жеткізу ақпараты</h2>
            <div class="info-row">
                <span class="info-label">Мекен-жай:</span>
                <span class="info-value"><?= htmlspecialchars($order['shipping_address']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Қала:</span>
                <span class="info-value"><?= htmlspecialchars($order['shipping_city']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Пошта индексі:</span>
                <span class="info-value"><?= htmlspecialchars($order['shipping_zip']) ?></span>
            </div>
        </div>
    </div>
    
    <div class="order-items-section">
        <h2>Тапсырыс элементтері</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Сурет</th>
                    <th>Тауар аты</th>
                    <th>Саны</th>
                    <th>Бірлік бағасы</th>
                    <th>Жалпы</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['image']): ?>
                                <img src="../uploads/products/<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50">
                            <?php else: ?>
                                <div class="no-image">Сурет жоқ</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price'], 0, ',', ' ') ?> ₸</td>
                        <td><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₸</td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4" class="text-right">Жалпы құны:</td>
                    <td><?= number_format($order['total'], 0, ',', ' ') ?> ₸</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="order-actions">
        <h2>Статусты өзгерту</h2>
        <form action="update_order_status.php" method="post">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <select name="status" class="form-select">
                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Күтуде</option>
                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Өңделуде</option>
                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Жеткізуде</option>
                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Аяқталды</option>
                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Бас тартылды</option>
            </select>
            <button type="submit" class="btn btn-primary">Сақтау</button>
        </form>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>