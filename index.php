<?php
session_start();
require_once 'php/config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userName = ($isLoggedIn && isset($_SESSION['username'])) ? $_SESSION['username'] : '';?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сувениры Востока | Главная</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #df6400;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Сувениры Востока</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <form method="GET" action="php/search.php" class="d-flex">
                        <input type="text" name="query" class="form-control me-2" placeholder="Поиск товара" required>
                        <button class="btn btn-outline-warning" type="submit">Поиск</button>
                    </form>
                    <a class="nav-link" href="catalog.php">Каталог</a>
                    <?php if ($isLoggedIn): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <?= htmlspecialchars($userName) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Профиль</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="php/logout.php">Выйти</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-outline-warning ms-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Войти
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Авторизация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form action="php/login.php" method="POST">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
                <div class="text-center mt-3">
                    <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">
                        Нет аккаунта? Зарегистрироваться
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Регистрация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form action="php/register.php" method="POST" id="registerForm">
                    <div class="mb-3">
                        <label for="registerName" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="registerName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="registerEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPhone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="registerPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="registerPassword" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPasswordConfirm" class="form-label">Подтверждение пароля</label>
                        <input type="password" class="form-control" id="registerPasswordConfirm" name="password_confirm" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</div>

<main class="container mt-5">
    <div class="row">
        <div class="col-md-6 align-self-center">
            <h1 class="display-4">Сувениры с Востока</h1>
            <p class="lead">Уникальные изделия, хранящие многовековые традиции восточных культур</p>
            <p>Каждый сувенир в нашем магазине — это частичка восточной мудрости и мастерства, созданная руками талантливых ремесленников.</p>
            <div class="mt-4">
                <a href="catalog.php" class="btn btn-warning btn-lg me-2">Каталог товаров</a>
                <?php if (!$isLoggedIn): ?>
                    <button class="btn btn-outline-warning btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Войти в аккаунт
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner rounded">
                    <div class="carousel-item active" data-bs-interval="3000">
                        <img src="assets/images/carousel1.jpg" class="d-block w-100" alt="Восточные сувениры">
                    </div>
                    <div class="carousel-item" data-bs-interval="3000">
                        <img src="assets/images/carousel2.jpg" class="d-block w-100" alt="Традиционные изделия">
                    </div>
                    <div class="carousel-item" data-bs-interval="3000">
                        <img src="assets/images/carousel3.jpg" class="d-block w-100" alt="Восточные специи">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-warning py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>О нас</h5>
                <p>Мы предлагаем подлинные сувениры и товары с Востока, привезенные непосредственно из стран происхождения.</p>
            </div>
            <div class="col-md-4">
                <h5>Контакты</h5>
                <p>Email: info@souveniry-vostoka.ru</p>
                <p>Телефон: +7 (123) 456-78-90</p>
            </div>
            <div class="col-md-4">
                <h5>Социальные сети</h5>
                <a href="#" class="text-dark me-2"><i class="bi bi-vk"></i> ВКонтакте</a><br>
                <a href="#" class="text-dark me-2"><i class="bi bi-telegram"></i> Telegram</a><br>
                <a href="#" class="text-dark me-2"><i class="bi bi-whatsapp"></i> WhatsApp</a>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p>&copy; 2025 Сувениры Востока. Все права защищены.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>