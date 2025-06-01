<?php
require_once 'DatabaseRepository.php';
require_once 'template_helpers.php';

$errors = $_SESSION['errors'] ?? [];
$values = $_SESSION['values'] ?? [];
$messages = $_SESSION['messages'] ?? [];

unset($_SESSION['errors'], $_SESSION['values'], $_SESSION['messages']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <?php if (!empty($_SESSION['login'])): ?>
            <div class="edit-notice">
                Режим редактирования. 
                <a href="index.php?logout=1" class="logout-link">Выйти и создать нового пользователя</a>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <?php if (!empty($messages)): ?>
                <div class="messages">
                <?php foreach ($messages as $message): ?>
                <div class="message">
                <?= !empty($message['raw_html']) ? $message['html'] : htmlspecialchars($message['html']) ?>
                </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="fio">ФИО:</label>
                <input type="text" id="fio" name="fio" value="<?= htmlspecialchars($values['fio'] ?? '') ?>" 
                       required pattern="[A-Za-zА-Яа-я\s]{1,150}" maxlength="150">
                <?php if (!empty($errors['full_name'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['full_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($values['phone'] ?? '') ?>" 
                       required pattern="\+7\d{10}">
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['phone']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($values['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="birth_date">Дата рождения:</label>
                <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($values['birth_date'] ?? '') ?>" required>
                <?php if (!empty($errors['birth_date'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['birth_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="male" <?= ($values['gender'] ?? '') === 'male' ? 'checked' : '' ?>>
                        Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="female" <?= ($values['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                        Женский
                    </label>
                </div>
                <?php if (!empty($errors['gender'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['gender']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Любимый язык программирования:</label>
                <?= renderSelectLanguages($values['languages'] ?? []) ?>
                <?php if (!empty($errors['languages'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['languages']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="biography">Биография:</label>
                <textarea id="biography" name="biography" required maxlength="500"><?= htmlspecialchars($values['biography'] ?? '') ?></textarea>
                <?php if (!empty($errors['biography'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['biography']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="contract_agreed" required <?= !empty($values['contract_agreed']) ? 'checked' : '' ?>>
                    С контрактом ознакомлен(а)
                </label>
                <?php if (!empty($errors['contract_agreed'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['contract_agreed']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Сохранить</button>
        </form>
    </main>
</body>
</html>