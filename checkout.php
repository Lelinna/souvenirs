<?php
session_start();
require_once 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: catalog.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    try {
        $db->beginTransaction();
        
        $userId = $_SESSION['user_id'];
        $totalAmount = 0;
        
        $productIds = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        $stmt = $db->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']]['quantity'];
            $totalAmount += $product['price'] * $quantity;
        }
        
        $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$userId, $totalAmount]);
        $orderId = $db->lastInsertId();
        
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']]['quantity'];
            
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                 VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $product['id'], $quantity, $product['price']]);
        }
        
        $db->commit();
        
        unset($_SESSION['cart']);
        
        header("Location: order_success.php?order_id=$orderId");
        exit;
        
    } catch (PDOException $e) {
        $db->rollBack();
        die("Ошибка при оформлении заказа: " . $e->getMessage());
    }
}

$stmt = $db->prepare("SELECT username, email, phone FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$productIds = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$stmt = $db->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
$stmt->execute($productIds);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartItems = [];
$total = 0;

foreach ($products as $product) {
    $quantity = $_SESSION['cart'][$product['id']]['quantity'];
    $subtotal = $product['price'] * $quantity;
    $total += $subtotal;
    
    $cartItems[] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $quantity,
        'subtotal' => $subtotal,
        'image' => $product['image'] ?? 'default.jpg'
    ];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа | Сувениры Востока</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <h2>Оформление заказа</h2>
                
                <form method="POST" action="checkout.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">ФИО</label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               value="<?= htmlspecialchars($user['username'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required 
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Адрес доставки</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comment" class="form-label">Комментарий к заказу</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-warning btn-lg">Подтвердить заказ</button>
                </form>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Ваш заказ</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>
                                        <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                                    </span>
                                    <span><?= number_format($item['subtotal'], 0, '', ' ') ?> ₽</span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between fw-bold">
                                <span>Итого</span>
                                <span><?= number_format($total, 0, '', ' ') ?> ₽</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>