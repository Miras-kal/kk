<?php
include '../../includes/config.php';

if (!is_admin()) {
    redirect('/');
}

$id = $_GET['id'] ?? redirect('/admin/products/');

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('/admin/products/');
}

$page_title = "Тауарды өңдеу: " . $product['name'];
include '../../includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $category = sanitize($_POST['category']);
    $stock = intval($_POST['stock']);
    
    $image = $product['image'];
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Ескі суретті жою
        if ($image !== 'default.jpg') {
            @unlink('../../assets/images/products/' . $image);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/images/products/' . $image);
    }
    
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category = ?, stock = ? WHERE id = ?");
    
    if ($stmt->execute([$name, $description, $price, $image, $category, $stock, $id])) {
        $_SESSION['success'] = 'Тауар сәтті жаңартылды!';
        redirect('/admin/products/');
    } else {
        $error = 'Тауарды жаңарту кезінде қате пайда болды';
    }
}
?>

<div class="admin-content">
    <h1>Тауарды өңдеу: <?= $product['name'] ?></h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Тауар аты*</label>
            <input type="text" id="name" name="name" value="<?= $product['name'] ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Сипаттама</label>
            <textarea id="description" name="description" rows="4"><?= $product['description'] ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="price">Бағасы* (₸)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?= $product['price'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="stock">Қоймадағы саны*</label>
                <input type="number" id="stock" name="stock" min="0" value="<?= $product['stock'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Санат</label>
                <input type="text" id="category" name="category" value="<?= $product['category'] ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Қазіргі сурет</label>
            <img src="/assets/images/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
            <label for="image">Жаңа суретті жүктеу</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сақтау</button>
            <a href="/admin/products/" class="btn btn-outline">Болдырмау</a>
        </div>
    </form>
</div>

<?php include '../../includes/admin_footer.php'; ?>