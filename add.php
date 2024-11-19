<?php
include 'db.php'; // Подключаем базу данных
session_start();

// Проверка наличия переменной $conn
if (!isset($conn)) {
    die("Ошибка: Переменная подключения не существует.");
}

$message = ''; // Переменная для хранения сообщений

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $server = $_POST['server'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Проверяем наличие user_id в сессии
    if (!isset($_SESSION['user_id'])) {
        die("Ошибка: Пользователь не авторизован.");
    }
    
    $user_id = $_SESSION['user_id']; // Вытаскиваем user_id из сессии

    // Подготовка SQL-запроса для добавления учетной записи
    $stmt = $conn->prepare("INSERT INTO credentials (user_id, server, username, password) VALUES (?, ?, ?, ?)");
    
    // Проверка на ошибки при подготовке запроса
    if (!$stmt) {
        die("Ошибка при подготовке запроса: " . $conn->error);
    }
    
    // Привязка параметров
    $stmt->bind_param("isss", $user_id, $server, $username, $password);
    
    if ($stmt->execute()) {
        $message = "Новая учетная запись добавлена успешно.";
    } else {
        $message = "Ошибка: " . $stmt->error;
    }
    
    // Закрытие подготовленного выражения
    $stmt->close();
}

// Закрытие соединения
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить учетную запись</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            margin-top: 20px;
        }
        input {
            display: block;
            margin-bottom: 10px;
            padding: 10px;
            width: 300px;
        }
        button {
            padding: 10px;
            width: 320px;
            cursor: pointer;
        }
        .message {
            margin: 10px 0;
            color: green;
        }
    </style>
</head>
<body>
    <h2>Добавить учетную запись</h2>
    
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="server" placeholder="Сервер" required>
        <input type="text" name="username" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Добавить</button>
    </form>
    <a href="index.php">Назад</a>

</body>
</html>
