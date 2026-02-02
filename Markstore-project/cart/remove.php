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

// Себеттен тауарды алып тастау
$stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);

if ($stmt->rowCount() > 0) {
    // Жаңа себет санын алу
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $count = $stmt->fetch()['count'] ?? 0;
    
    echo json_encode(['success' => true, 'count' => $count]);
} else {
    echo json_encode(['success' => false, 'message' => 'Тауар себетте табылмады']);
}
?>