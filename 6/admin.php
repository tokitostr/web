<?php
require_once 'DatabaseRepository.php';
require_once 'template_helpers.php';

session_start();

$db = new DatabaseRepository();
$db->validateAdminCredentials();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        if ($db->deleteUser($_POST['delete'])) {
            $_SESSION['admin_message'] = 'Пользователь успешно удален';
        } else {
            $_SESSION['admin_error'] = 'Ошибка при удалении пользователя';
        }
        header('Location: admin.php');
        exit();
    } elseif (isset($_POST['edit'])) {
        $_SESSION['edit_id'] = $_POST['edit'];
        header('Location: edit.php');
        exit();
    }
}

$applications = $db->getAllApplications();
$language_stats = $db->getLanguageStatistics();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="admin-container">
        <h1>Админ-панель</h1>
        
        <?php if (!empty($_SESSION['admin_message'])): ?>
            <div class="admin-message"><?= htmlspecialchars($_SESSION['admin_message']) ?></div>
            <?php unset($_SESSION['admin_message']); ?>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['admin_error'])): ?>
            <div class="admin-error"><?= htmlspecialchars($_SESSION['admin_error']) ?></div>
            <?php unset($_SESSION['admin_error']); ?>
        <?php endif; ?>
        
        <h2>Все заявки</h2>
        <?= renderTable($applications, [
            'id' => ['title' => 'ID'],
            'full_name' => ['title' => 'ФИО'],
            'phone' => ['title' => 'Телефон'],
            'email' => ['title' => 'Email'],
            'birth_date' => ['title' => 'Дата рождения'],
            'gender' => ['title' => 'Пол', 'formatter' => fn($v) => $v == 'male' ? 'Мужской' : 'Женский'],
            'languages_list' => ['title' => 'Языки'],
            'biography' => ['title' => 'Биография', 'formatter' => fn($v) => truncateText($v, 50)],
            'contract_agreed' => ['title' => 'Контракт', 'formatter' => fn($v) => $v ? 'Да' : 'Нет']
        ], [
            ['name' => 'edit', 'title' => 'Редактировать'],
            ['name' => 'delete', 'title' => 'Удалить']
        ]) ?>
        
        <h2>Статистика по языкам</h2>
        <?= renderTable($language_stats, [
            'language_name' => ['title' => 'Язык'],
            'user_count' => ['title' => 'Количество пользователей']
        ]) ?>
    </div>
<script>
document.querySelectorAll('button[name="delete"]').forEach(btn => {
    btn.addEventListener('click', (e) => {
        if (!confirm('Вы уверены, что хотите удалить этого пользователя?')) {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>