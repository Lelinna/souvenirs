<?php
require_once('config.php');

if (isset($_GET['query'])) {
    $searchQuery = trim($_GET['query']);
    
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE :search OR description LIKE :search");
        $searchTerm = '%' . $searchQuery . '%';
        
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) > 0) {
            echo '<h1>Результаты поиска для: ' . htmlspecialchars($searchQuery) . '</h1>';
            echo '<div class="row">';

            foreach ($products as $product) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card h-100">';
                echo '<img src="assets/products"' . htmlspecialchars($product['image']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '">';
                echo '<div class="card-body d-flex flex-column">';
                echo '<h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';
                echo '<p class="card-text flex-grow-1">' . htmlspecialchars($product['description']) . '</p>';
                echo '<p class="product-price fw-bold">' . number_format($product['price'], 0, '', ' ') . ' руб.</p>';
                echo '<a href="add_to_cart.php?id=' . $product['id'] . '" class="btn btn-outline-warning mt-auto">Добавить в корзину</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>'; 
            }

            echo '</div>'; 
        } else {
            echo '<div class="alert alert-info">Товары не найдены.</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Ошибка при выполнении поиска: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-warning">Запрос не может быть пустым.</div>';
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сувениры Востока | Главная</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
</html>