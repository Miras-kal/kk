<?php
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('/auth/login.php?return_url=/cart/');
}

$page_title = "Себет";
include '../includes/header.php';

// Себеттегі тауарларды алу
$stmt = $pdo->prepare("
    SELECT ci.*, p.name, p.price, p.image, p.stock 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Жалпы құнын есептеу
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="cart-container">
    <h1>Сіздің себетіңіз</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Себетіңіз бос</p>
            <a href="/index.php" class="btn btn-primary">Тауарларға өту</a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <table>
                <thead>
                    <tr>
                        <th>Тауар</th>
                        <th>Бағасы</th>
                        <th>Саны</th>
                        <th>Құны</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr data-product-id="<?= $item['product_id'] ?>">
                            <td class="product-info">
                                <img src="/assets/images/products/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                                <div>
                                    <h4><?= $item['name'] ?></h4>
                                    <?php if ($item['stock'] < $item['quantity']): ?>
                                        <p class="stock-warning">Қоймада тек <?= $item['stock'] ?> дана қалды</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="price"><?= number_format($item['price'], 0, ',', ' ') ?> ₸</td>
                            <td class="quantity">
                                <button class="decrease-quantity" data-product-id="<?= $item['product_id'] ?>">-</button>
                                <input type="number" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" 
                                       class="quantity-input" data-product-id="<?= $item['product_id'] ?>">
                                <button class="increase-quantity" data-product-id="<?= $item['product_id'] ?>">+</button>
                            </td>
                            <td class="subtotal"><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₸</td>
                            <td class="remove">
                                <button class="remove-item" data-product-id="<?= $item['product_id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="cart-summary">
            <div class="summary-card">
                <h3>Себет қорытындысы</h3>
                <div class="summary-row">
                    <span>Тауарлар құны:</span>
                    <span id="subtotal"><?= number_format($total, 0, ',', ' ') ?> ₸</span>
                </div>
                <div class="summary-row">
                    <span>Жеткізу құны:</span>
                    <span id="delivery">0 ₸</span>
                </div>
                <div class="summary-row total">
                    <span>Барлығы:</span>
                    <span id="grand-total"><?= number_format($total, 0, ',', ' ') ?> ₸</span>
                </div>
                <a href="/checkout/" class="btn btn-primary checkout-button">Сатып алуды рәсімдеу</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="/assets/js/cart.js"></script>
<?php include '../includes/footer.php'; ?>