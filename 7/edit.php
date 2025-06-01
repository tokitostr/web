<?php
require_once 'DatabaseRepository.php';
require_once 'Validator.php';
require_once 'template_helpers.php';

session_start();

$db = new DatabaseRepository();
$db->validateAdminCredentials();

$edit_id = $_SESSION['edit_id'] ?? null;
if (!$edit_id) {
    header('Location: admin.php');
    exit();
}

$user_data = $db->getUser($edit_id);
$user_languages = $db->getUserLanguages($edit_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = Validator::validateUserForm($_POST);
    
    if (empty($errors)) {
        $db->updateUser($edit_id, $_POST);
        header('Location: admin.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <div class="edit-container">
        <h2>Редактирование пользователя #<?= htmlspecialchars($edit_id) ?></h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <?= renderFormField('text', 'full_name', 'ФИО', $errors ?? [], $user_data, ['required' => '']) ?>
            <?= renderFormField('tel', 'phone', 'Телефон', $errors ?? [], $user_data, ['required' => '']) ?>
            <?= renderFormField('email', 'email', 'Email', $errors ?? [], $user_data, ['required' => '']) ?>
            <?= renderFormField('date', 'birth_date', 'Дата рождения', $errors ?? [], $user_data, ['required' => '']) ?>
            
            <label>Пол:</label>
            <?= renderRadioField('gender', 'male', 'Мужской', $user_data) ?>
            <?= renderRadioField('gender', 'female', 'Женский', $user_data) ?>
            <?= !empty($errors['gender']) ? '<div class="error-message">' . htmlspecialchars($errors['gender']) . '</div>' : '' ?>
            
            <label>Любимый язык программирования:</label>
            <?= renderSelectLanguages($user_languages) ?>
            <?= !empty($errors['languages']) ? '<div class="error-message">' . htmlspecialchars($errors['languages']) . '</div>' : '' ?>
            
            <?= renderTextarea('biography', 'Биография', $errors ?? [], $user_data, ['required' => '', 'maxlength' => '500']) ?>
            
            <label>
                <input type="checkbox" name="contract_agreed" <?= $user_data['contract_agreed'] ? 'checked' : '' ?>>
                Согласен с контрактом
            </label>
            <?= !empty($errors['contract_agreed']) ? '<div class="error-message">' . htmlspecialchars($errors['contract_agreed']) . '</div>' : '' ?>
            
            <button type="submit">Сохранить изменения</button>
            <a href="admin.php" class="cancel-button">Отмена</a>
        </form>
    </div>
</body>
</html>