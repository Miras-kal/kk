<?php
include '../../includes/config.php';

// Тек әкімшілерге рұқсат ету
if (!is_admin()) {
    redirect('/');
}

$page_title = "Тауарларды басқару";
include '../../includes/admin_header.php';

// Тауарларды алу
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>Тауарларды басқару</h1>
        <a href="/admin/products/add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Жаңа тауар
        </a>
    </div>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Сурет</th>
                <th>Аты</th>
                <th>Бағасы</th>
                <th>Қоймада</th>
                <th>Әрекеттер</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td>
                        <img src="/assets/images/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-thumb">
                    </td>
                    <td><?= $product['name'] ?></td>
                    <td><?= number_format($product['price'], 0, ',', ' ') ?> ₸</td>
                    <td><?= $product['stock'] ?></td>
                    <td class="actions">
                        <a href="/admin/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="/admin/products/delete.php" method="POST" class="delete-form">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-delete" onclick="return confirm('Тауарды шынымен жойғыңыз келе ме?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/admin_footer.php'; ?>