<?php
/**
 * Тауарларды алу функциясы
 * 
 * @param int|null $limit - Тауарлар санының шегі
 * @param string|null $category - Тауар санаты
 * @param string|null $search - Іздеу сөзі
 * @return array - Тауарлар массиві
 */
function get_products($limit = null, $category = null, $search = null) {
    global $pdo;
    
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (name LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Тауарды ID бойынша алу
 * 
 * @param int $id - Тауар ID
 * @return array|false - Тауар массиві немесе false
 */
function get_product_by_id($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Пайдаланушыны ID бойынша алу
 * 
 * @param int $id - Пайдаланушы ID
 * @return array|false - Пайдаланушы массиві немесе false
 */
function get_user_by_id($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Себеттегі тауарлар санын алу
 * 
 * @return int - Себеттегі тауарлар саны
 */
function get_cart_count() {
    if (!is_logged_in()) return 0;
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

/**
 * Пайдаланушы тапсырыстарын алу
 * 
 * @param int $user_id - Пайдаланушы ID
 * @return array - Тапсырыстар массиві
 */
function get_user_orders($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT o.*, COUNT(oi.id) as items_count 
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Тапсырыс элементтерін алу
 * 
 * @param int $order_id - Тапсырыс ID
 * @return array - Тапсырыс элементтері массиві
 */
function get_order_items($order_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}

/**
 * Тапсырыстың толық ақпаратын алу
 * 
 * @param int $order_id - Тапсырыс ID
 * @return array|false - Тапсырыс массиві немесе false
 */
function get_order_details($order_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email, u.phone, u.address 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetch();
}

/**
 * Парольді хэштеу
 * 
 * @param string $password - Пароль
 * @return string - Хэштелген пароль
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Парольді тексеру
 * 
 * @param string $password - Енгізілген пароль
 * @param string $hash - Хэштелген пароль
 * @return bool - Сәйкес келе ме
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Әкімші панелінің header файлын қосу
 */
function include_admin_header() {
    include dirname(__DIR__) . '/includes/admin_header.php';
}

/**
 * Әкімші панелінің footer файлын қосу
 */
function include_admin_footer() {
    include dirname(__DIR__) . '/includes/admin_footer.php';
}

/**
 * Әртүрлі статустарға сәйкес badge стильдері
 * 
 * @param string $status - Статус
 * @return string - Стиль класы
 */
function get_status_badge_class($status) {
    switch ($status) {
        case 'pending':
            return 'badge-warning';
        case 'processing':
            return 'badge-info';
        case 'completed':
            return 'badge-success';
        case 'cancelled':
            return 'badge-danger';
        default:
            return 'badge-secondary';
    }
}

/**
 * Күнді әдемі форматта көрсету
 * 
 * @param string $date - Күн (MySQL форматта)
 * @return string - Пішімделген күн
 */
function format_date($date) {
    return date('d.m.Y H:i', strtotime($date));
}

/**
 * Санды ақша форматта көрсету
 * 
 * @param float $amount - Сома
 * @return string - Пішімделген сома
 */
function format_price($amount) {
    return number_format($amount, 0, ',', ' ') . ' ₸';
}
?>