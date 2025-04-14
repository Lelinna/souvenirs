<?php
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['username'] ?? 'Пользователь') : '';
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #df6400;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Сувениры Востока</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="catalog.php">Каталог</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-light position-relative me-2">
                        Корзина
                        <?php if ($cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $cartCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php if ($isLoggedIn): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                <?= htmlspecialchars($userName) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Профиль</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="php/logout.php">Выйти</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light">Войти</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
