<?php
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /products/");
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container'><p>Тауар табылмады.</p></div>";
    include 'includes/footer.php';
    exit;
}
?>

<style>
.container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px 20px;
}

.product-details {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
}

.product-details img {
    width: 100%;
    max-width: 400px;
    border-radius: 10px;
    object-fit: cover;
}

.product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.product-info h1 {
    font-size: 28px;
    margin-bottom: 20px;
}

.product-info p {
    margin-bottom: 14px;
    font-size: 16px;
}

.product-info .price {
    font-size: 24px;
    color: #2ecc71;
    margin-bottom: 10px;
}

.product-info .stock {
    font-size: 16px;
    color: #7f8c8d;
    margin-bottom: 16px;
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    background: #3498db;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.3s;
}

.btn:hover {
    background: #2980b9;
}
</style>

<div class="container">
    <div class="product-details">
        <img src="/assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

        <div class="product-info">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p class="price"><?= number_format($product['price'], 0, ',', ' ') ?> ₸</p>
            <p class="stock">Қоймада: <?= $product['stock'] ?> дана</p>
            <p><strong>Санаты:</strong> <?= htmlspecialchars($product['category']) ?></p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <a href="/index.php" class="btn">← Артқа қайту</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
