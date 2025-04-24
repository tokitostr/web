<?php
header('Content-Type: text/html; charset=UTF-8');

if (!session_id()) {
    session_start();
}

if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = 'u42690';
    $pass = '7522024';
    $db = new PDO('mysql:host=localhost;dbname=u42690', $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $db->prepare("SELECT id, pass FROM application WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data || md5($_POST['pass']) !== $user_data['pass']) {
        $error_message = 'Неверный логин или пароль.'; 
    } else {
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['uid'] = $user_data['id'];
        header('Location: index.php');
        exit();
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
        <form action="login.php" method="post">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="pass" placeholder="Пароль" required>
            <input type="submit" value="Войти">
        </form>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <a href="index.php" class="back-link">Вернуться на главную</a>
    </div>
</body>
</html>
