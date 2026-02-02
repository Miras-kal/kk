<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Конфигурацияны қосу
include 'includes/config.php';

$page_title = "Басты бет";
include 'includes/header.php'; 
?>

<!-- Стиль -->
<style>
html {
  scroll-behavior: smooth;
}
</style>

<!-- Басты баннер -->
<section class="hero">
    <h1>MARKSTORE <br>Үздік тауарлар бір ғана жерде</h1>
    <p>Жоғары сапалы өнімдерді бізбен бірге сатып алыңыз</p>
    <a href="#all-products" class="btn btn-primary">Тауарларды қарау</a>
</section>

<!-- Барлық тауарлар бөлімі -->
<section id="all-products" class="featured-products">
    <h2 class="section-title">Барлық тауарлар</h2>

    <div class="product-grid">
        <?php
        // Базадан барлық тауарларды алу
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        while ($product = $stmt->fetch()):
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="/assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                <?php if ($product['stock'] <= 0): ?>
                    <span class="out-of-stock">Сатылымда жоқ</span>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p class="product-price"><?= number_format($product['price'], 0, ',', ' ') ?> ₸</p>

                <?php if ($product['stock'] > 0): ?>
                    <button class="add-to-cart" data-product-id="<?= $product['id'] ?>">
                        <i class="fas fa-cart-plus"></i> Себетке қосу
                    </button>
                <?php else: ?>
                    <button class="notify-me" data-product-id="<?= $product['id'] ?>">
                        <i class="fas fa-bell"></i> Хабарлау
                    </button>
                <?php endif; ?>

                <a href="/product.php?id=<?= $product['id'] ?>" class="view-details">
                    <i class="fas fa-eye"></i> Толығырақ
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
