<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


include '../../includes/config.php';

// Тек әкімшілерге рұқсат ету
if (!is_admin()) {
    redirect('/');
}

// Тауардың ID-н тексеру
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $product_id = $_POST['id'];

    // Өнімді базадан жою
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    // Егер жою сәтті болса, қайта бағыттау
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Тауар сәтті жойылды!";
    } else {
        $_SESSION['message'] = "Қателік орын алды. Тауарды жою мүмкін болмады.";
    }
} else {
    $_SESSION['message'] = "Қате: Тауар ID белгісіз.";
}

// Жоюдан кейін әкімші парақшасына қайта бағыттау
header('Location: /admin/products');
exit;
?>
