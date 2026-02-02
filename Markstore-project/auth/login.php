<?php
session_start(); // <--- міндетті, сессия жұмыс істеуі үшін

include '../includes/config.php';

if (is_logged_in()) {
    redirect('/');
}

$page_title = "Кіру";
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Қарапайым парольді тексеру (хэштеусіз)
    if ($user && $user['password'] === $password) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        $return_url = $_GET['return_url'] ?? '/';
        redirect($return_url);
    } else {
        $error = "Қате email немесе құпия сөз";
    }
}
?>

<div class="auth-container">
    <h1>Кіру</h1>
    
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
