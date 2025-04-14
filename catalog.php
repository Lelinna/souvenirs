<?php
session_start();
require_once 'php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$productId] = [
            'quantity' => 1,
            'product_id' => $productId
        ];
    }
    
    header('Location: catalog.php' . (isset($_GET['category']) ? '?category=' . $_GET['category'] : ''));
    exit;
}

try {
    $categoriesStmt = $db->query("SELECT * FROM categories");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при получении категорий: " . $e->getMessage());
}

$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : null;
$whereClause = $selectedCategory ? "WHERE p.category_id = :category_id" : "";
$orderBy = "ORDER BY p.name ASC";

try {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              $whereClause 
              $orderBy";
    
    $stmt = $db->prepare($query);
    
    if ($selectedCategory) {
        $stmt->bindParam(':category_id', $selectedCategory, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при получении товаров: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров | Сувениры Востока</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .category-filter {
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Каталог товаров</h1>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4 category-filter">
                    <div class="card-body">
                        <h4>Фильтры</h4>
                        <form method="GET" action="">
                            <div class="mb-3">
                                <label for="category" class="form-label">Категория</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Все товары</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                            <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Применить</button>
                            <?php if ($selectedCategory): ?>
                                <a href="catalog.php" class="btn btn-outline-secondary w-100 mt-2">Сбросить</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <h4>
                        <?= $selectedCategory ? 
                            "Товары в категории: " . htmlspecialchars(
                                $categories[array_search($selectedCategory, array_column($categories, 'id'))]['name']
                            ) : 
                            'Все товары' 
                        ?>
                        <small class="text-muted">(<?= count($products) ?>)</small>
                    </h4>

                </div>
                
                <?php if (empty($products)): ?>
                    <div class="alert alert-info">
                        Товары не найдены. Попробуйте изменить параметры фильтра.
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col">
                                <div class="card h-100 product-card">
                                    <img src="assets/products/<?= htmlspecialchars($product['image'] ?? 'default.jpg') ?>" 
                                         class="card-img-top p-3" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                        <p class="card-text text-muted small">
                                            Категория: <?= htmlspecialchars($product['category_name']) ?>
                                        </p>
                                        <p class="card-text">
                                            <?= mb_strimwidth(htmlspecialchars($product['description']), 0, 100, '...') ?>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="h5 text-warning">
                                                <?= number_format($product['price'], 0, '', ' ') ?> ₽
                                            </span>
                                            <form method="POST" action="catalog.php<?= $selectedCategory ? '?category=' . $selectedCategory : '' ?>">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" class="btn btn-outline-warning">
                                                    В корзину
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>