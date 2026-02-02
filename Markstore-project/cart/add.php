<?php
include '../includes/config.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Тіркелу керек']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, stock FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Тауар табылмады']);
    exit;
}

if ($product['stock'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Тауар қоймада жоқ']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$existing_item = $stmt->fetch();

if ($existing_item) {
    $new_quantity = $existing_item['quantity'] + 1;

    if ($new_quantity > $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Қоймада жеткілікті саны жоқ']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $existing_item['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$user_id, $product_id]);
}

$stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$count = $stmt->fetch()['count'];

echo json_encode(['success' => true, 'count' => $count]);
?>