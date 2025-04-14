<?php
session_start();
require_once 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['order_id'])) {
    header('Location: profile.php');
    exit;
}

$orderId = intval($_GET['order_id']);
$userId = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("SELECT o.*, u.username, u.email, u.phone 
                         FROM orders o
                         JOIN users u ON o.user_id = u.id
                         WHERE o.id = ? AND o.user_id = ?");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: profile.php');
        exit;
    }

    $stmt = $db->prepare("SELECT oi.*, p.name, p.image 
                         FROM order_items oi
                         JOIN products p ON oi.product_id = p.id
                         WHERE oi.order_id = ?");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Ошибка при получении данных заказа: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен | Сувениры Востока</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-card {
            border-left: 4px solid #df6400;
        }
        .status-badge {
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card order-card">
                    <div class="card-header bg-warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="mb-0">Заказ успешно оформлен!</h2>
                            <span class="badge bg-success status-badge">
                                <?= $order['status'] == 'pending' ? 'В обработке' : 'Подтвержден' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Спасибо за ваш заказ!</h4>
                            <p>Мы отправили детали заказа на вашу электронную почту <strong><?= htmlspecialchars($order['email']) ?></strong>.</p>
                            <hr>
                            <p class="mb-0">Номер вашего заказа: <strong>#<?= $orderId ?></strong></p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>Информация о заказе</h4>
                                <p><strong>Номер заказа:</strong> #<?= $orderId ?></p>
                                <p><strong>Дата оформления:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                                <p><strong>Статус:</strong> 
                                    <span class="badge bg-<?= $order['status'] == 'pending' ? 'warning' : 'success' ?>">
                                        <?= $order['status'] == 'pending' ? 'В обработке' : 'Подтвержден' ?>
                                    </span>
                                </p>
                                <p><strong>Сумма заказа:</strong> <?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Данные покупателя</h4>
                                <p><strong>Имя:</strong> <?= htmlspecialchars($order['username']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                                <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone'] ?? 'не указан') ?></p>
                            </div>
                        </div>

                        <h4 class="mb-3">Состав заказа</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Товар</th>
                                        <th>Цена</th>
                                        <th>Количество</th>
                                        <th>Итого</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/products/<?= htmlspecialchars($item['image'] ?? 'default.jpg') ?>" 
                                                     width="50" class="me-3">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </div>
                                        </td>
                                        <td><?= number_format($item['price'], 0, '', ' ') ?> ₽</td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= number_format($item['price'] * $item['quantity'], 0, '', ' ') ?> ₽</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Общая сумма:</strong></td>
                                        <td><strong><?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h5>Информация о доставке</h5>
                                <p>Мы свяжемся с вами в ближайшее время для уточнения деталей доставки.</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="catalog.php" class="btn btn-outline-secondary me-md-2">Вернуться в каталог</a>
                            <a href="profile.php" class="btn btn-warning">Мои заказы</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>