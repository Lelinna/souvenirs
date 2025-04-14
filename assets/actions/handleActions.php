<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($_POST['action']) {
            case 'add_product':
                break;
                
            case 'edit_product':
                break;
                
            case 'toggle_product_status':
                $stmt = $db->prepare("UPDATE products SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $_SESSION['success'] = "Статус товара изменен";
                break;
                
            case 'delete_product':
                $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $_SESSION['success'] = "Товар удален";
                break;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Ошибка: " . $e->getMessage();
    }
    
    header("Location: adminPanel.php");
    exit;
}
?>
