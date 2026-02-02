<?php
include '../includes/config.php';

if (is_logged_in()) {
    redirect('/');
}

$page_title = "Тіркелу";
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Валидация
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Пайдаланушы атын енгізіңіз";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Дұрыс email енгізіңіз";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Бұл email бос емес";
        }
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Құпия сөз кемінде 6 таңбадан тұруы керек";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Құпия сөздер сәйкес келмейді";
    }
    
    if (empty($errors)) {
        // БҰЛ ЖЕРДЕ ЕШ ХЕШ ЖОҚ — таза пароль
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            $_SESSION['success'] = "Тіркелу сәтті аяқталды! Жүйеге кіріңіз";
            redirect('/auth/login.php');
        } else {
            $errors[] = "Тіркелу кезінде қате пайда болды";
        }
    }
}
?>

<div class="auth-container">
    <h1>Тіркелу</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="auth-form">
        <div class="form-group">
            <label for="username">Пайдаланушы аты*</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Құпия сөз*</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Құпия сөзді растау*</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Тіркелу</button>
        </div>
    </form>
    
    <div class="form-footer">
        <p>Аккаунтыңыз бар ма? <a href="/auth/login.php">Кіру</a></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
