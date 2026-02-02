<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Интернет-дүкен'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Тегіс анимация үшін */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <a href="/" class="logo">
                <img src="https://i.ytimg.com/vi/zDXhqG5u6l8/hqdefault.jpg" alt="Логотип">
                <span>Markstore</span>
            </a>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item"><a href="/" class="nav-link">Басты</a></li>
                    <!-- Тауарлар батырмасын өзгерту -->
                    <li class="nav-item"><a href="#all-products" class="nav-link">Тауарлар</a></li>

                    <?php if (is_logged_in()): ?>
                        <li class="nav-item">
                            <a href="/cart/" class="nav-link">
                                <i class="fas fa-shopping-cart"></i> Себет (<span id="cart-count">0</span>)
                            </a>
                        </li>
                        <li class="nav-item"><a href="/auth/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Шығу</a></li>

                        <?php if (is_admin()): ?>
                            <li class="nav-item"><a href="/admin/dashboard.php" class="nav-link"><i class="fas fa-cogs"></i> Админ панелі</a></li>
                        <?php endif; ?>

                    <?php else: ?>
                        <li class="nav-item"><a href="/auth/login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Кіру</a></li>
                        <li class="nav-item"><a href="/auth/register.php" class="nav-link"><i class="fas fa-user-plus"></i> Тіркелу</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
