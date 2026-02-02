<?php
include '../includes/config.php';
session_start();

if (!is_logged_in()) {
    redirect('/auth/login.php?return_url=/checkout/');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/checkout/');
}

// Жеткізу ақпаратын алу
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$address = $_POST['address'];
$notes = $_POST['notes'];

// Қолданушының себетін оқу
$stmt = $pdo->prepare("
    SELECT ci.*, p.price
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    redirect('/cart/');
}

// Тапсырыс сомасын есептеу
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$delivery = 0;
$total = $subtotal + $delivery;

// Тапсырысты базаға қосу
$stmt = $pdo->prepare("
    INSERT INTO orders (user_id, full_name, phone, email, address, notes, total, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
");
$stmt->execute([
    $_SESSION['user_id'], $full_name, $phone, $email, $address, $notes, $total
]);

$order_id = $pdo->lastInsertId();

// Тапсырыс сәтті жазылса, session-ға сақтаймыз
$_SESSION['order_success'] = $order_id;

// Төлем әдісі бетіне көшеміз
redirect('/checkout/payment-method.php');
?>
