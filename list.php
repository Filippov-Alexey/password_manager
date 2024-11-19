<?php
session_start(); 
include 'db.php';

if (!isset($conn)) {
    die("Ошибка подключения к базе данных.");
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Получаем user_id из сессии
$user_id = $_SESSION['user_id'];

// Подготовка SQL-запроса с фильтрацией по user_id
$stmt = $conn->prepare("SELECT * FROM credentials WHERE user_id = ?");
$stmt->bind_param("i", $user_id); // Привязываем параметр user_id как целое число

// Выполняем запрос
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    echo '<form method="POST" action="">'; // Открытие формы

    while ($row = $result->fetch_assoc()) {
        echo "Сервер: " . htmlspecialchars($row['server']) . "<br>";
        
        // Поле для логина
        echo "Логин: <input type='text' name='username[]' value='" . htmlspecialchars($row['username']) . "' readonly id='username_" . $row['id'] . "'>";
        echo "<button type='button' onclick=\"copyToClipboard('username_" . $row['id'] . "')\">Копировать</button><br>";
        
        // Поле для пароля
        echo "Пароль: <input type='password' name='password_' value='" . htmlspecialchars($row['password']) . "' readonly id='password_" . $row['id'] . "'>";
        echo "<button type='button' onclick=\"copyToClipboard('password_" . $row['id'] . "')\">Копировать</button><br><br>";
    }

    echo '</form>'; // Закрытие формы
} else {
    echo "Ошибка выполнения запроса: " . $conn->error;
}

// Закрытие соединения
$stmt->close();
$conn->close();
?>

<?php
echo('<h2>Пользователь: ' . htmlspecialchars($_SESSION['username']) . '</h2><br>');
echo('<h2>id: ' . htmlspecialchars($_SESSION['user_id']) . '</h2><br>');
?>

<a href="add.php">Добавить новую учетную запись</a>
<a href="index.php">Назад</a>

<script>
function copyToClipboard(elementId) {
    // Получаем текстовое поле
    var copyText = document.getElementById(elementId);
    
    // Создаем временное текстовое поле для копирования
    var tempInput = document.createElement("input");
    tempInput.value = copyText.value; // Устанавливаем в него значение поля
    document.body.appendChild(tempInput); // Добавляем его на страницу
    tempInput.select(); // Выбираем текст в поле
    
    // Копируем текст в буфер обмена
    document.execCommand("copy"); // Копируем

    // Удаляем временное поле
    document.body.removeChild(tempInput);
}
</script>
