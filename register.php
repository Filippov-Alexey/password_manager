<?php
include 'db.php'; // Подключаем файл для подключения к БД

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $security_questions = $_POST['security_question'];
    $security_answers = $_POST['security_answer'];

    // Проверка на наличие пользователя
    $sql_check_user = "SELECT * FROM user WHERE username = ?";
    $stmt_check_user = $conn->prepare($sql_check_user);
    $stmt_check_user->bind_param("s", $username);
    $stmt_check_user->execute();
    $check_result = $stmt_check_user->get_result();

    if ($check_result->num_rows > 0) {
        echo "Ошибка: Имя пользователя уже занято.";
    } else {
        // Вставляем пользователя в таблицу user
        $sql = "INSERT INTO user (username) VALUES (?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id; // Получаем ID последнего добавленного пользователя

                // Теперь добавляем вопросы безопасности
                $sql_q = "INSERT INTO security_questions (user_id, question, answer) VALUES (?, ?, ?)";
                $stmt_q = $conn->prepare($sql_q);
                if ($stmt_q) {
                    foreach ($security_questions as $index => $question) {
                        $answer_hashed = password_hash($security_answers[$index], PASSWORD_DEFAULT);
                        $stmt_q->bind_param("iss", $user_id, $question, $answer_hashed);
                        $stmt_q->execute();
                    }
                    $stmt_q->close();
                }
                echo "Регистрация успешна!";
            } else {
                echo "Ошибка: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    $stmt_check_user->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <script>
        function addSecurityQuestion() {
            var container = document.getElementById("security-questions");
            var newQuestion = document.createElement("div");
            newQuestion.innerHTML = `
                <label>Вопрос безопасности:</label>
                <input type="text" name="security_question[]" required>
                <label>Ответ на вопрос:</label>
                <input type="text" name="security_answer[]" required>
                <button type="button" onclick="removeSecurityQuestion(this)">Удалить</button>
                <br>
            `;
            container.appendChild(newQuestion);
        }

        function removeSecurityQuestion(button) {
            button.parentElement.remove();
        }
    </script>
</head>
<body>
    <h2>Регистрация</h2>
    <form method="POST" action="">
        <label for="username">Имя пользователя:</label><br>
        <input type="text" id="username" name="username" required><br>
        
        <div id="security-questions">
            <h3>Вопросы безопасности:</h3>
            <div>
                <label>Вопрос безопасности:</label>
                <input type="text" name="security_question[]" required>
                <label>Ответ на вопрос:</label>
                <input type="text" name="security_answer[]" required>
                <br>
            </div>
        </div>
        
        <button type="button" onclick="addSecurityQuestion()">Добавить вопрос безопасности</button>
        <br><br>
        
        <input type="submit" value="Зарегистрироваться">
    </form>
</body>
</html>
