<?php
include '../../includes/config.php';

// Тек админдерге рұқсат
if (!is_logged_in() || !is_admin()) {
    redirect('../../auth/login.php');
}

// POST сұранысты өңдеу
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email    = sanitize($_POST['email']);
    $phone    = sanitize($_POST['phone']);
    $password = sanitize($_POST['password']); // хеш жоқ
    $role     = sanitize($_POST['role']);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $email, $phone, $password, $role]);

        redirect('index.php');
    } catch (PDOException $e) {
        error_log("User insert error: " . $e->getMessage());
        $error = "Қате пайда болды, қайталап көріңіз.";
    }
}

$page_title = "Жаңа пайдаланушы";
include '../../includes/admin_header.php';
?>

<div class="admin-content">
    <h1>Жаңа пайдаланушы қосу</h1>

    <?php if (isset($error)) : ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="admin-form">
        <div class="form-group">
            <label for="username">Аты-жөні</label>
            <input type="text" id="username" name="username" placeholder="Аты-жөні" required>
        </div>

        <div class="form-group">
            <label for="email">Электронды пошта</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <label for="phone">Телефон нөмірі</label>
            <input type="text" id="phone" name="phone" placeholder="+7 777 777 77 77" required>
        </div>

        <div class="form-group">
            <label for="password">Құпия сөз</label>
            <input type="text" id="password" name="password" placeholder="Құпия сөз" required>
        </div>

        <div class="form-group">
            <label for="role">Рөлі</label>
            <select name="role" id="role" required>
                <option value="">Рөлді таңдаңыз</option>
                <option value="user">Қарапайым</option>
                <option value="admin">Админ</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Қосу</button>
    </form>
</div>

<?php include '../../includes/admin_footer.php'; ?>
