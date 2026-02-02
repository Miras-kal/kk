<?php
include 'config.php';

// Тек әкімшілерге рұқсат ету
if (!is_admin()) {
    redirect('/');
}
?>
<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Әкімші панелі'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            position: fixed;
            height: 100%;
            padding-top: 20px;
        }
        
        .admin-sidebar .logo {
            text-align: center;
            padding: 10px;
            margin-bottom: 30px;
        }
        
        .admin-sidebar .logo img {
            height: 40px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 10px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-menu .active a {
            color: white;
            background-color: var(--primary);
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: flex;
            overflow: hidden;
        }
        
        .stat-icon {
            width: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .stat-info {
            padding: 20px;
            flex-grow: 1;
        }
        
        .stat-info h3 {
            font-size: 16px;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .stat-info p {
            font-size: 24px;
            font-weight: bold;
        }
        
        .dashboard-sections {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-warning { background-color: var(--warning); color: var(--dark); }
        .badge-info { background-color: var(--info); color: white; }
        .badge-success { background-color: var(--success); color: white; }
        .badge-danger { background-color: var(--danger); color: white; }
        .badge-secondary { background-color: var(--gray); color: white; }
        
        .btn-view {
            background-color: var(--info);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="logo">
            <img src="https://i.ytimg.com/vi/zDXhqG5u6l8/hqdefault.jpg" alt="Логотип">
        </div>
        
        <ul class="sidebar-menu">
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <a href="/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Басқару тақтасы</a>
            </li>
            <li class="<?= strpos($_SERVER['PHP_SELF'], '/products/') !== false ? 'active' : '' ?>">
                <a href="/admin/products/"><i class="fas fa-box"></i> Тауарлар</a>
            </li>
            <li class="<?= strpos($_SERVER['PHP_SELF'], '/orders/') !== false ? 'active' : '' ?>">
                <a href="/admin/orders/"><i class="fas fa-shopping-cart"></i> Тапсырыстар</a>
            </li>
            <li class="<?= strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active' : '' ?>">
                <a href="/admin/users/"><i class="fas fa-users"></i> Пайдаланушылар</a>
            </li>
            <li>
                <a href="/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Шығу</a>
            </li>
        </ul>
    </div>
    
    <div class="admin-main">