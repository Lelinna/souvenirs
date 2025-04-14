<?php
session_start();
require_once 'php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
    } elseif (isset($_POST['remove'])) {
        $productId = intval($_POST['product_id']);
        unset($_SESSION['cart'][$productId]);
    }
    
    header('Location: cart.php');
    exit;
}

$cartItems = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']]['quantity'];
            $itemTotal = $product['price'] * $quantity;
            
            $cartItems[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'total' => $itemTotal,
                'image' => $product['image'] ?? 'default.jpg'
            ];
            
            $totalPrice += $itemTotal;
        }
    } catch (PDOException $e) {
        die("Ошибка при получении товаров: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина | Сувениры Востока</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .quantity-input {
            width: 60px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <h1 class="mb-4">Ваша корзина</h1>
        
        <?php if (!empty($cartItems)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Товар</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Итого</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="assets/products/<?= htmlspecialchars($item['image']) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                             width="60" class="me-3">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </div>
                                </td>
                                <td><?= number_format($item['price'], 0, '', ' ') ?> ₽</td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <input type="number" name="quantity" 
                                               value="<?= $item['quantity'] ?>" 
                                               min="1" class="form-control quantity-input">
                                        <button type="submit" name="update" class="btn btn-sm btn-outline-secondary ms-2">
                                            Обновить
                                        </button>
                                    </form>
                                </td>
                                <td><?= number_format($item['total'], 0, '', ' ') ?> ₽</td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="remove" class="btn btn-sm btn-danger">
                                            Удалить
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Общая сумма:</strong></td>
                            <td colspan="2"><strong><?= number_format($totalPrice, 0, '', ' ') ?> ₽</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="catalog.php" class="btn btn-outline-secondary">Продолжить покупки</a>
                <a href="checkout.php" class="btn btn-warning">Оформить заказ</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Ваша корзина пуста. <a href="catalog.php" class="alert-link">Перейти в каталог</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>