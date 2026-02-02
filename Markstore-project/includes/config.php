<?php




// 1. Сессияны бастау (қайталануды болдырмау үшін)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. База параметрлері (константалар)
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4'); // utf8 орнына utf8mb4 қолданыңыз

// 3. PDO байланысы
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // SQL инъекциясынан қорғаныс
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("DB Connection Error: ".$e->getMessage());
    die("Қазіргі уақытта жүйеде техникалық қызмет жүргізілуде. Кейінірек көріңіз.");
}

// 4. Функциялар (қайта анықтаудан қорғау)
if (!function_exists('redirect')) {
    function redirect($url, $permanent = false) {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return !empty($_SESSION['user_id']);
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        if (!is_logged_in()) return false;
        
        // Базадан тексеру (сессияға сенбеу)
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            return $user && $user['is_admin'] == 1;
        } catch (PDOException $e) {
            error_log("Admin check error: ".$e->getMessage());
            return false;
        }
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data) {
        if (is_array($data)) {
            return array_map('sanitize', $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// 5. Қауіпсіздік баптаулары
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header_remove('X-Powered-By');

