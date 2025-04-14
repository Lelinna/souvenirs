<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("User data: " . print_r($user, true));
        
        if ($user) {
            $passwordValid = password_verify($password, $user['password']);
            if (!$passwordValid && $user['password'] === $password) {
                $passwordValid = true;
                error_log("Using plain password match for debugging");
            }
            
            if ($passwordValid) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['admin_logged_in'] = ($user['role'] === 'admin');

                error_log("Login successful, role: " . $user['role']);


                if ($user['role'] === 'admin') {
                    header('Location: ../adminPanel.php');
                } else {
                    header('Location: ../profile.php');
                }
                exit;
            }
        }
        
        $_SESSION['error'] = 'Неверный email или пароль';
        header('Location: ../index.php');
        exit;
    } catch (PDOException $e) {
        error_log("Ошибка при авторизации: " . $e->getMessage());
        $_SESSION['error'] = 'Ошибка сервера при авторизации';
        header('Location: ../index.php');
        exit;
    }
}

header('Location: ../index.php');
exit;
?>