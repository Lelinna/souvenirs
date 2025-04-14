<?php
session_start();
require_once 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function getStatusText($status) {
    switch ($status) {
        case 'pending': return 'В обработке';
        case 'completed': return 'Завершен';
        case 'cancelled': return 'Отменен';
        default: return $status;
    }
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (empty($username) || empty($email)) {
        $error = 'Имя и email обязательны для заполнения';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email';
    } else {
        try {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $error = 'Этот email уже используется другим пользователем';
            } else {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$username, $email, $phone, $userId]);
                $success = 'Данные успешно обновлены';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка при обновлении данных: ' . $e->getMessage();
        }
    }
}

$stmt = $db->prepare("SELECT username, email, phone, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$orders = []; 
try {
    $stmt = $db->prepare("
        SELECT o.id, o.total_amount, o.status, o.created_at, 
               COUNT(oi.id) as items_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orders = []; 
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль | Сувениры Востока</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-pending { color: #ffc107; }
        .status-completed { color: #28a745; }
        .status-cancelled { color: #dc3545; }
        .order-card {
            transition: all 0.3s ease;
            border-left: 4px solid #df6400;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .edit-mode .view-data { display: none; }
        .edit-mode .edit-data { display: block; }
        .edit-data { display: none; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <h4>Мой профиль</h4>
                        <button id="toggleEdit" class="btn btn-sm btn-light">Редактировать</button>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        
                        <div class="text-center mb-3">
                            <div>
                                <img src="assets/users/<?= htmlspecialchars($product['image'] ?? 'default.jpg') ?>" style="width: 100px; height: 100px;">
                            </div>
                        </div>
                        
                        <form method="POST" id="profileForm">
                            <div class="view-data">
                                <p><strong>Имя:</strong> <?= htmlspecialchars($user['username']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone'] ?? 'не указан') ?></p>
                                <p><strong>Дата регистрации:</strong> <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                            </div>
                            
                            <div class="edit-data">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Имя</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Телефон</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="button" id="cancelEdit" class="btn btn-secondary">Отмена</button>
                                    <button type="submit" name="update_profile" class="btn btn-warning">Сохранить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h4>Мои заказы</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($orders)): ?>
                            <div class="list-group">
                                <?php foreach ($orders as $order): ?>
                                    <a href="order_success.php?id=<?= $order['id'] ?>" 
                                       class="list-group-item list-group-item-action order-card mb-2">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5>Заказ #<?= $order['id'] ?></h5>
                                                <p class="mb-1">
                                                    <strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Товаров:</strong> <?= $order['items_count'] ?>
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <p class="mb-1">
                                                    <strong>Сумма:</strong> <?= number_format($order['total_amount'], 0, '', ' ') ?> ₽
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Статус:</strong>
                                                    <span class="status-<?= $order['status'] ?>">
                                                        <?= getStatusText($order['status']) ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                У вас пока нет заказов. <a href="catalog.php" class="alert-link">Перейти в каталог</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleEdit = document.getElementById('toggleEdit');
            const cancelEdit = document.getElementById('cancelEdit');
            const profileForm = document.getElementById('profileForm');
            
            toggleEdit.addEventListener('click', function() {
                profileForm.classList.toggle('edit-mode');
                if (profileForm.classList.contains('edit-mode')) {
                    toggleEdit.textContent = 'Просмотр';
                } else {
                    toggleEdit.textContent = 'Редактировать';
                }
            });
            
            cancelEdit.addEventListener('click', function() {
                profileForm.classList.remove('edit-mode');
                toggleEdit.textContent = 'Редактировать';
            });
        });
    </script>
</body>
</html>