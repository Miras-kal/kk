<?php
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('/auth/login.php');
}

session_start();

$order_id = $_SESSION['order_success'] ?? null;

if ($order_id === null) {
    redirect('/checkout/');
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

$page_title = "Тапсырыс сәтті рәсімделді";
include '../includes/header.php';
?>

<style>
.order-success-container {
    max-width: 600px;
    margin: 50px auto;
    padding: 35px;
    background-color: #f4f9f9;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

.order-success-container h1 {
    font-size: 30px;
    margin-bottom: 25px;
    color: #28a745;
}

.order-success-container p {
    font-size: 18px;
    margin: 10px 0;
    color: #333;
}

.order-success-container p:nth-child(2) {
    font-weight: bold;
}

.order-success-container p:last-child {
    margin-top: 20px;
    font-style: italic;
    color: #555;
}
</style>

<div class="order-success-container">
    <h1>Тапсырысыңыз сәтті қабылданды!</h1>

    <p>Тапсырыс нөмірі: <?= htmlspecialchars($order['id']) ?></p>
    <p>Төлем әдісі: <?= htmlspecialchars($order['payment_method']) ?></p>
    <p>Барлық сомасы: <?= number_format($order['total'], 0, ',', ' ') ?> ₸</p>

    <p>Тапсырысыңыз 7 күн ішінде жеткізіледі.</p>
</div>

<?php include '../includes/footer.php'; ?>
