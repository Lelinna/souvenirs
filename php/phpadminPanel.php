<?php
    session_start();

    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../index.php");
        exit;
    }

    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $db->beginTransaction();

            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add_product':
                        $stmt = $db->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
                        $imageName = uploadImage('image', 'products');
                        $stmt->execute([
                            $_POST['name'],
                            $_POST['description'],
                            $_POST['price'],
                            $imageName
                        ]);
                        $_SESSION['success'] = "Товар успешно добавлен";
                        break;

                    case 'edit_product':
                        $imageName = uploadImage('image', 'products');
                        if ($imageName) {
                            $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
                            $stmt->execute([
                                $_POST['name'],
                                $_POST['description'],
                                $_POST['price'],
                                $imageName,
                                $_POST['id']
                            ]);
                        } else {
                            $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
                            $stmt->execute([
                                $_POST['name'],
                                $_POST['description'],
                                $_POST['price'],
                                $_POST['id']
                            ]);
                        }
                        $_SESSION['success'] = "Товар успешно обновлен";
                        break;

                    case 'delete_product':
                        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        $_SESSION['success'] = "Товар успешно удален";
                        break;

                    case 'update_user':
                        $imageName = uploadImage('image', 'users');
                        if ($imageName) {
                            $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, phone = ?, role = ?, image = ? WHERE id = ?");
                            $stmt->execute([
                                $_POST['username'],
                                $_POST['email'],
                                $_POST['phone'],
                                $_POST['role'],
                                $imageName,
                                $_POST['id']
                            ]);
                        } else {
                            $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, phone = ?, role = ? WHERE id = ?");
                            $stmt->execute([
                                $_POST['username'],
                                $_POST['email'],
                                $_POST['phone'],
                                $_POST['role'],
                                $_POST['id']
                            ]);
                        }
                        $_SESSION['success'] = "Пользователь успешно обновлен";
                        break;

                    case 'delete_user':
                        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        $_SESSION['success'] = "Пользователь успешно удален";
                        break;

                    case 'update_order':
                        $stmt = $db->prepare("UPDATE orders SET user_id = ?, status = ? WHERE id = ?");
                        $stmt->execute([
                            $_POST['user_id'] ?: null,
                            $_POST['status'],
                            $_POST['order_id']
                        ]);
                        $_SESSION['success'] = "Заказ успешно обновлен";
                        break;
                }
            }

            $db->commit();
            header("Location: ../adminPanel.php");
            exit;
        } catch (PDOException $e) {
            $db->rollBack();
            $_SESSION['error'] = "Ошибка: " . $e->getMessage();
            header("Location: ../adminPanel.php");
            exit;
        }
    }

    function uploadImage($fieldName, $folder) {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "assets/$folder/";
            $fileExt = pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExt;
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadFile)) {
                return $fileName;
            }
        }
        return null;
    }

    require_once 'includes/getData.php';
?>
