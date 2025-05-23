<?php
require_once 'DatabaseRepository.php';

session_start();
header('Content-Type: text/html; charset=UTF-8');

if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error_message = '';
    
    if (empty($_POST['login']) || empty($_POST['pass'])) {
        $error_message = 'Логин и пароль обязательны для заполнения';
    } else {
        $db = new DatabaseRepository();
        $user = $db->checkUserCredentials($_POST['login'], $_POST['pass']);
        
        if ($user) {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['uid'] = $user['id'];
            header('Location: index.php?edit=1');
            exit();
        } else {
            $error_message = 'Неверный логин или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Вход в систему</h1>
        <form method="post">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="pass" placeholder="Пароль" required>
            <input type="submit" value="Войти">
        </form>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <a href="index.php" class="back-link">Вернуться на главную</a>
    </div>
</body>
</html>