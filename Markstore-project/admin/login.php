<?php
// config.php файлын қосу
include '../includes/config.php';

// Егер админ жүйеге кірген болса, негізгі бетке бағыттау
if (is_logged_in() && $_SESSION['is_admin'] === 1) {
    redirect('orders/detalis.php'); // немесе сіздің админ панелі жолыңыз
}

$page_title = "Админге кіру";
include '../includes/header.php';

// Егер форма жіберілсе
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Пайдаланушының мәліметтерін алу
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Құпия сөзді тексеру
    if ($user && password_verify($password, $user['password']) && $user['is_admin'] == 1) {
        // Егер дұрыс болса, админді сессияға қосу
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        $return_url = $_GET['return_url'] ?? '/dashboard.php'; // Админ панелі
        redirect($return_url);
    } else {
        // Қате хабарын көрсету
        $error = "Қате email немесе құпия сөз, немесе сіз админ емессіз!";
    }
}
?>

<div class="auth-container">
    <h1>Админге кіру</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Құпия сөз</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Кіру</button>
        </div>
    </form>
    
    <div class="form-footer">
        <p>Тіркелмедіңіз бе? <a href="/auth/register.php">Тіркелу</a></p>
        <p>Құпия сөзді ұмыттыңыз ба? <a href="/auth/forgot.php">Қалпына келтіру</a></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
