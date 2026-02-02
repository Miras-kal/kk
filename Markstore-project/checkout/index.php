<?php
include '../includes/config.php';
session_start();

if (!is_logged_in()) {
    redirect('/auth/login.php?return_url=/checkout/');
}

$page_title = "Тапсырыс рәсімдеу";
include '../includes/header.php';

$stmt = $pdo->prepare("
    SELECT ci.*, p.name, p.price, p.stock 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    redirect('/cart/');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$delivery = 0;
$total = $subtotal + $delivery;
?>

<div class="checkout-container">
    <h1>Тапсырыс рәсімдеу</h1>

    <form action="/checkout/payment-method.php" method="post" id="checkout-form">
        <div class="checkout-columns">
            <div class="delivery-info">
                <h2>Жеткізу ақпараты</h2>

                <div class="form-group">
                    <label for="full_name">Толық аты-жөні*</label>
                    <input type="text" id="full_name" name="full_name" required 
                        value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Телефон нөмірі* (+ Вассап)</label>
                    <input type="tel" id="phone" name="phone" required 
                        value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="email" id="email" name="email" required 
                        value="<?= htmlspecialchars($user['email']) ?>">
                </div>

                <div class="form-group">
                    <label for="address">Жеткізу мекен-жайы*</label>
                    <textarea id="address" name="address" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Қосымша ескертулер</label>
                    <textarea id="notes" name="notes" rows="2"></textarea>
                </div>
            </div>

            <div class="order-summary">
                <h2>Тапсырыс қорытындысы</h2>

                <div class="summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <span class="item-name"><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                            <span class="item-price"><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₸</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-totals">
                    <div class="total-row">
                        <span>Тауарлар құны:</span>
                        <span><?= number_format($subtotal, 0, ',', ' ') ?> ₸</span>
                    </div>
                    <div class="total-row">
                        <span>Жеткізу құны:</span>
                        <span><?= number_format($delivery, 0, ',', ' ') ?> ₸</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Барлығы:</span>
                        <span><?= number_format($total, 0, ',', ' ') ?> ₸</span>
                    </div>
                </div>

                <button type="submit" class="place-order-btn">Келесі қадам</button>
            </div>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
