<?php
include '../../includes/config.php';

if (!is_admin()) {
    redirect('/');
}

$page_title = "Жаңа тауар қосу";
include '../../includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $category = sanitize($_POST['category']);
    $stock = intval($_POST['stock']);
    
    // Суретті жүктеу
    $image = 'default.jpg';
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/images/products/' . $image);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category, stock) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $description, $price, $image, $category, $stock])) {
            $_SESSION['success'] = 'Тауар сәтті қосылды!';
            redirect('/index.php'); // Изменено перенаправление
        } else {
            $error = 'Тауарды қосу кезінде қате пайда болды';
        }
    } catch (PDOException $e) {
        $error = 'Дерекқор қатесі: ' . $e->getMessage();
    }
}
?>

<div class="admin-content">
    <h1>Жаңа тауар қосу</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="product-form">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Тауар аты*</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="price">Бағасы* (₸)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required class="form-control">
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Сипаттама</label>
            <textarea id="description" name="description" rows="4" class="form-control"></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="stock">Қоймадағы саны*</label>
                <input type="number" id="stock" name="stock" min="0" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="category">Санат</label>
                <select id="category" name="category" class="form-control">
                    <option value="Электроника">Электроника</option>
                    <option value="Киім">Киім</option>
                    <option value="Ас">Ас</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="image">Сурет</label>
            <input type="file" id="image" name="image" accept="image/*" class="form-control-file">
            <small class="form-text text-muted">Ұсынылатын өлшем: 800x800 пиксель</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сақтау</button>
            <a href="/index.php" class="btn btn-outline-secondary">Болдырмау</a>
        </div>
    </form>
</div>

<?php include '../../includes/admin_footer.php'; ?>