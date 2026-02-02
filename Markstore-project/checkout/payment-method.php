<?php
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('/auth/login.php?return_url=/checkout/');
}

session_start();

// Формадан келген мәліметтерді алу
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $notes = $_POST['notes'];

    // Тапсырыс жасау
    $subtotal = 0;
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

    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    $delivery = 0;
    $total = $subtotal + $delivery;

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, phone, email, address, notes, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([
        $_SESSION['user_id'], $full_name, $phone, $email, $address, $notes, $total
    ]);

    $order_id = $pdo->lastInsertId();

    $_SESSION['order_success'] = $order_id;

} elseif (!isset($_SESSION['order_success'])) {
    redirect('/checkout/');
}

$page_title = "Төлем әдісін таңдау";
include '../includes/header.php';
?>

<style>
.payment-method-container {
    max-width: 500px;
    margin: 40px auto;
    padding: 30px;
    background: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.payment-method-container h1 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

select {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
}

.confirm-payment-btn {
    background-color: #28a745;
    color: #fff;
    padding: 14px 28px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    margin-top: 20px;
    display: inline-block;
    width: 100%;
}

.confirm-payment-btn:hover {
    background-color: #218838;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    transform: translateY(-2px);
}
</style>

<div class="payment-method-container">
    <h1>Төлем әдісін таңдау</h1>

    <form action="/checkout/process.php" method="post" id="payment-form">
        <div class="form-group">
            <label for="payment_method">Төлем әдісі*</label>
            <select id="payment_method" name="payment_method" required>
                <option value="card">Карта арқылы</option>
                <option value="cash_on_delivery">Қолма-қол төлем</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>

        <button type="submit" class="confirm-payment-btn">Төлемді растау</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
