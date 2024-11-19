<?php
// Начинаем сессию
session_start();

// Удаляем все данные сессии
$_SESSION = array();

// Если нужно уничтожить сессию полностью, удаляем и куку сессии
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на страницу входа или на главную страницу
header("Location: login.php"); // Замените на вашу страницу входа
exit;
