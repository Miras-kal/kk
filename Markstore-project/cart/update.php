<?php
include '../includes/config.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Тіркелу керек']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$quantity = intval($data['quantity']);
$user_id = $_SESSION['user_id'];

// Санды тексеру
if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Саны 1-ден кем болмауы керек']);
    exit;
}

// Тауардың бар екенін және қоймада жеткілікті саны бар екенін тексеру
$stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Тауар табылмады']);
    exit;
}

if ($quantity > $product['stock']) {
    echo json_encode(['success' => false, 'message' => 'Қоймада жеткілікті саны жоқ']);
    exit;
}

// Себетті жаңарту
$stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
$stmt->execute([$quantity, $user_id, $product_id]);

// Жаңа себетті алу
$stmt = $pdo->prepare("
    SELECT ci.quantity, p.price 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Жаңа құндарды есептеу
$total_items = 0;
$subtotal = 0;

foreach ($cart_items as $item) {
    $total_items += $item['quantity'];
    $subtotal += $item['price'] * $item['quantity'];
}

echo json_encode([
    'success' => true,
    'total_items' => $total_items,
    'subtotal' => $subtotal,
    'cart' => $cart_items
]);
?>