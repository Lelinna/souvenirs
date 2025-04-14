<?php
$productsCount = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$stmt = $db->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usersCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stmt_users = $db->prepare("SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt_users->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt_users->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_users->execute();
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

$ordersCount = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$stmt_orders = $db->prepare("
    SELECT o.*, u.username as user_name 
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC 
    LIMIT :limit OFFSET :offset
");
$stmt_orders->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt_orders->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_orders->execute();
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

?>