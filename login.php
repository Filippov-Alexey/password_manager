<?php
session_start();
include 'db.php';

$security_question = '';
$security_question_id = '';
$message = '';
$username = '';
$user_id = NULL;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['check_user'])) {
        $username = trim($_POST['username']);

        // Получаем ответы на вопросы
        if ($stmt = $conn->prepare("
            SELECT security_questions.answer 
            FROM security_questions 
            JOIN user ON security_questions.user_id = user.id 
            WHERE user.username = ? 
            LIMIT 0, 25;")) {

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Определяем массив для хранения ответов
                $answers = [];
                while ($row = $result->fetch_assoc()) {
                    $answers[] = htmlspecialchars($row["answer"]);
                }
                $message .= "<br><br>Ответы:<br>" . implode("<br>", $answers);
            } else {
                $message .= "Нет пользователя '$username'.";
            }
        }

        // Получаем вопросы и user_id
        if ($stmt = $conn->prepare("
            SELECT security_questions.question, security_questions.id, user.id as user_id
            FROM security_questions 
            JOIN user ON security_questions.user_id = user.id 
            WHERE user.username = ? 
            LIMIT 0, 25;")) {

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result1 = $stmt->get_result();

            if ($result1->num_rows > 0) {
                // Список вопросов
                $questions = $result1->fetch_all(MYSQLI_ASSOC);
                $random_question = $questions[array_rand($questions)];
                $security_question = htmlspecialchars($random_question['question']);
                $security_question_id = $random_question['id'];
                
                // Сохраняем user_id в переменную
                $user_id = $random_question['user_id'];
                
                // Выводим данные
                echo($security_question.';'.$security_question_id.';'.$user_id.'<br>');
            } else {
                $message .= "Ошибка с вопросом.";
            }
        } else {
            $message .= "Ошибка с ответом.";
        }
    }

    // Проверка введённого ответа
    if (isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $entered_answer = trim($_POST['security_answer']);
        $answers = [];

        $stmt = $conn->prepare("
            SELECT security_questions.answer 
            FROM security_questions 
            JOIN user ON security_questions.user_id = user.id 
            WHERE user.username = ? 
            LIMIT 0, 25;"); 

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $answers[] = htmlspecialchars($row["answer"]);
        }

        // Проверяем, соответствует ли введенный ответ
        foreach ($answers as $answer) {
            if (password_verify($entered_answer, $answer)) { 
                // Сохраняем данные в сессии
                if ($stmt = $conn->prepare("
            SELECT security_questions.question, security_questions.id, user.id as user_id
            FROM security_questions 
            JOIN user ON security_questions.user_id = user.id 
            WHERE user.username = ? 
            LIMIT 0, 25;")) {

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result1 = $stmt->get_result();

            if ($result1->num_rows > 0) {
                // Список вопросов
                $questions = $result1->fetch_all(MYSQLI_ASSOC);
                $random_question = $questions[array_rand($questions)];
                $security_question = htmlspecialchars($random_question['question']);
                $security_question_id = $random_question['id'];
                
                // Сохраняем user_id в переменную
                $user_id = $random_question['user_id'];
        
                $_SESSION['username'] = $username; 
                $_SESSION['user_id'] = $user_id; // Теперь user_id будет иметь значение
                echo($user_id);
                header("Location: index.php");
                exit(); 
            }
        }
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .error { color: red; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Вход</h2>
    <?php if ($message): ?>
        <div class="error"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($security_question === ''): ?>
        <form method="POST" action="">
            <label for="username">Имя пользователя:</label><br>
            <input type="text" id="username" name="username" required><br>
            <input type="submit" name="check_user" value="Проверить">
        </form>
    <?php else: ?>
        <form method="POST" action="">
            <label for="username">Имя пользователя:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username']); ?>" required readonly><br>
            <span><?php echo $security_question; ?></span><br>
            <label>Ответ:</label><br>
            <input type="text" name="security_answer" required><br>
            <input type="submit" name="login" value="Войти">
        </form>
    <?php endif; ?>
</body>
</html>
