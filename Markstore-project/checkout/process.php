<?php
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('/auth/login.php?return_url=/checkout/');
}

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/checkout/payment-method.php');
}

$order_id = $_SESSION['order_success'] ?? null;

if ($order_id === null) {
    redirect('/checkout/');
}

try {
    $payment_method = $_POST['payment_method'];

    $stmt = $pdo->prepare("UPDATE orders SET status = 'confirmed', payment_method = ? WHERE id = ?");
    $stmt->execute([$payment_method, $order_id]);

    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    redirect('/checkout/order-success.php');
} catch (Exception $e) {
    $_SESSION['error'] = "Төлем кезінде қате: " . $e->getMessage();
    redirect('/checkout/payment-method.php');
}
?>
