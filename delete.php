<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM credentials WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Учетная запись удалена успешно.";
    } else {
        echo "Ошибка: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
<a href="list.php">Вернуться к списку учетных записей</a> 