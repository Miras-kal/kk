<?php
include '../../includes/config.php';

// Тек админдерге рұқсат
if (!is_logged_in() || !is_admin()) {
    redirect('../../auth/login.php');
}

// id бар-жоғын тексеру
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$user_id = (int)$_GET['id'];

// Пайдаланушыны жою
try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
} catch (PDOException $e) {
    error_log("User delete error: " . $e->getMessage());
    // Қате болса да, басты бетке қайту
}

redirect('index.php');
?>
