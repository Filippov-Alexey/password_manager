<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $server = $_POST['server'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE credentials SET server = ?, username = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $server, $username, $hashedPassword, $id);
    
    if ($stmt->execute()) {
        echo "Учетная запись обновлена успешно.";
    } else {
        echo "Ошибка: " . $stmt->error;
    }
    $stmt->close();
} else if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM credentials WHERE id = $id");
    $row = $result->fetch_assoc();
}

$conn->close();
?>
<form method="POST" action="">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <input type="text" name="server" placeholder="Сервер" value="<?php echo $row['server']; ?>" required>
    <input type="text" name="username" placeholder="Логин" value="<?php echo $row['username']; ?>" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Обновить</button>
</form>
