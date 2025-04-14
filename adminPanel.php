<?php include 'php/phpadminPanel.php'; ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель Администратора | Сувениры Востока</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php include 'includes/notifications.php'; ?>
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Панель управления</h1>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Товары</h5>
                                <p class="card-text display-4"><?= $productsCount ?></p>
                                <a href="#manageProducts" class="text-white">Управление товарами</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Пользователи</h5>
                                <p class="card-text display-4"><?= $usersCount ?></p>
                                <a href="#manageUsers" class="text-white">Управление пользователями</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Заказы</h5>
                                <p class="card-text display-4"><?= $ordersCount ?></p>
                                <a href="#manageOrders" class="text-white">Управление заказами</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-content mt-4 ms-0 ps-0 manage-products-container">
                    <div id="manageProducts" class="tab-pane fade show active">
                        <div class="d-flex justify-content-between mb-3">
                            <h2>Управление товарами</h2>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="bi bi-plus-circle"></i> Добавить товар
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Изображение</th>
                                        <th>Название</th>
                                        <th>Цена</th>
                                        <th>Категория</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= $product['id'] ?></td>
                                        <td>
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="assets/products/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="50">
                                            <?php else: ?>
                                                <span class="text-muted">Нет фото</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= number_format($product['price'], 2, '.', ' ') ?> ₽</td>
                                        <td><?= htmlspecialchars($product['category_id']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-product" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editProductModal"
                                                    data-product='<?= json_encode($product) ?>'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="adminPanel.php" class="d-inline">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить этот товар?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="manageUsers" class="tab-pane fade show active">
                        <div class="d-flex justify-content-between mb-3">
                            <h2>Управление пользователями</h2>
                        </div>

                        <?php if (empty($users)): ?>
                            <div class="alert alert-info">Нет пользователей для отображения</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Имя</th>
                                            <th>Email</th>
                                            <th>Телефон</th>
                                            <th>Роль</th>
                                            <th>Дата регистрации</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $user['id'] ?></td>
                                                <td><?= htmlspecialchars($user['username']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><?= htmlspecialchars($user['phone'] ?? 'не указан') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                                        <?= $user['role'] === 'admin' ? 'Админ' : 'Пользователь' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary edit-user" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user='<?= json_encode($user) ?>'>
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" action="adminPanel.php" class="d-inline">
                                                        <input type="hidden" name="action" value="delete_user">
                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Удалить этого пользователя?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                                        
                    <div id="manageOrders" class="tab-pane fade show active">
                        <div class="d-flex justify-content-between mb-3">
                            <h2>Управление заказами</h2>
                        </div>

                        <?php if (empty($orders)): ?>
                            <div class="alert alert-info">Нет заказов для отображения</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Пользователь</th>
                                            <th>Дата</th>
                                            <th>Сумма</th>
                                            <th>Статус</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?= $order['id'] ?></td>
                                            <td>
                                                <?= htmlspecialchars($order['user_name'] ?? 'Гость') ?>
                                            </td>
                                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                            <td><?= number_format($order['total_amount'], 2, '.', ' ') ?> ₽</td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $order['status'] === 'completed' ? 'success' : 
                                                    ($order['status'] === 'cancelled' ? 'danger' : 'warning') 
                                                ?>">
                                                    <?= $order['status'] === 'completed' ? 'Завершен' : 
                                                        ($order['status'] === 'cancelled' ? 'Отменен' : 'В обработке') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-order" data-bs-toggle="modal" data-bs-target="#editOrderModal" data-order='<?= json_encode($order) ?>'>
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="order_success.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="adminPanel.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_product"> 
                    <div class="modal-header">
                        <h5 class="modal-title">Добавить товар</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Цена</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Изображение</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="adminPanel.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_product">
                    <input type="hidden" name="id" id="editProductId">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Редактировать товар</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                    <div class="mb-3">
                            <label class="form-label">Название</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Цена</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Изображение</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="adminPanel.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" id="editUserId">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Редактировать пользователя</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Имя пользователя</label>
                            <input type="text" name="username" id="editUsername" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Телефон</label>
                            <input type="tel" name="phone" id="editPhone" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Роль</label>
                            <select name="role" id="editRole" class="form-select">
                                <option value="user">Пользователь</option>
                                <option value="admin">Администратор</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Аватар</label>
                            <input type="file" name="image" class="form-control">
                            <div class="mt-2">
                                <img src="" id="editUserAvatar" class="rounded-circle" width="50" height="50" style="display: none;">
                                <small class="text-muted">Текущее изображение</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="adminPanel.php">
                    <input type="hidden" name="action" value="update_order">
                    <input type="hidden" name="order_id" id="editOrderId">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrderModalLabel">Редактировать заказ #<span id="orderNumberDisplay"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Покупатель</label>
                                    <select name="user_id" id="editOrderUserId" class="form-select">
                                        <option value="">Гость</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= $user['email'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Статус</label>
                                    <select name="status" id="editOrderStatus" class="form-select">
                                        <option value="pending">В обработке</option>
                                        <option value="processing">В работе</option>
                                        <option value="shipped">Отправлен</option>
                                        <option value="completed">Завершен</option>
                                        <option value="cancelled">Отменен</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="js/adminPanel.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html>