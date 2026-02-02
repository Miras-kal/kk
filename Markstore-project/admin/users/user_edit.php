<?php
include '../../includes/config.php';

// Тек админ рұқсат
if (!is_logged_in() || !is_admin()) {
    redirect('../../auth/login.php');
}

// id тексеру
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$user_id = (int)$_GET['id'];

// Пайдаланушыны алу
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        redirect('index.php');
    }
} catch (PDOException $e) {
    error_log("User fetch error: " . $e->getMessage());
    redirect('index.php');
}

// Форма жіберілсе
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email    = sanitize($_POST['email']);
    $phone    = sanitize($_POST['phone']);
    $role     = sanitize($_POST['role']);

    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $phone, $role, $user_id]);
    } catch (PDOException $e) {
        error_log("User update error: " . $e->getMessage());
    }

    redirect('index.php');
}

// Бет атауы
$page_title = "Пайдаланушыны өңдеу";
include '../../includes/admin_header.php';
?>

<div class="admin-content">
    <h1>Пайдаланушыны өңдеу</h1>

    <form method="POST" class="admin-form">
        <div class="form-group">
            <label for="username">Аты</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Рөлі</label>
            <select name="role" id="role" required>
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Қарапайым</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Админ</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Сақтау</button>
    </form>
</div>

<?php include '../../includes/admin_footer.php'; ?>

<!-- CSS стильдер -->
<style>
    .admin-content {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .admin-content h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
        display: block;
    }

    .form-group input, .form-group select {
        width: 100%;
        padding: 12px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus, .form-group select:focus {
        border-color: #007bff;
        outline: none;
    }

    button.btn.btn-primary {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: #fff;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button.btn.btn-primary:hover {
        background-color: #0056b3;
    }
</style>
