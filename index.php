<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Менеджер паролей</title>
</head>
<body>
    <h1>Добро пожаловать в Менеджер Паролей</h1>
    
    <?php if (isset($_SESSION['username'])): ?>
        <?php
        echo('<h2>Пользователь: '.$_SESSION['username'].'</h2><br>');
        ?>
        <a href="list.php">Просмотр учетных записей</a><br>
        <a href="add.php">Добавить новую учетную запись</a><br>
        <a href="logout.php">Выйти</a><br>
    <?php else: ?>
        <a href="login.php">Вход</a><br>
        <a href="register.php">Регистрация</a><br>
    <?php endif; ?>
</body>
</html>
