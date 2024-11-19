<?php
$host = '';
$user = '';
$password = ''; 
$database = 'password_manager';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Ошибка подключения: ' . $conn->connect_error);
}
?>
