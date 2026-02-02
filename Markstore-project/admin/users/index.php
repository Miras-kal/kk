<?php
// Конфигурацияны қосу
include '../../includes/config.php';

// Админ тексеру (рөл тексеру орнына is_admin() қолданамыз)
if (!is_logged_in() || !is_admin()) {
    redirect('../../auth/login.php');
}

// Бет атауы
$page_title = "Пайдаланушылар";

// Хедерді қосу
include '../../includes/admin_header.php';

// Пайдаланушыларды базадан алу
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    $total_users = count($users);
} catch (PDOException $e) {
    error_log("Users fetch error: " . $e->getMessage());
    $users = [];
    $total_users = 0;
}
?>

<div class="admin-content">
    <h1>Пайдаланушылар <span class="badge"><?= $total_users ?></span></h1>

    <div class="admin-actions">
        <a href="user_add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Жаңа пайдаланушы
        </a>
    </div>

    <div class="users-table">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Аты</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Рөлі</th>
                    <th>Тіркелген күні</th>
                    <th>Әрекет</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= $user['role'] ?></td>
                            <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="user_delete.php?id=<?= $user['id'] ?>" class="btn btn-delete" onclick="return confirm('Жойғыңыз келе ме?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">Пайдаланушылар табылмады</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>
