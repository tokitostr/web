<?php
header('Content-Type: text/html; charset=UTF-8');


echo '<link rel="stylesheet" href="styles.css">';


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (!empty($_GET['save'])) {
        echo '<p style="color: green;">Спасибо, результаты сохранены.</p>';
    }


    include('form.php');
    exit();
}

$errors = false;


if (empty($_POST['fio']) || !preg_match('/^[A-Za-zА-Яа-я\s]{1,150}$/u', $_POST['fio'])) {
    echo '<p style="color: red;">Заполните корректно ФИО (только буквы и пробелы, не более 150 символов).</p>';
    $errors = true;
}

if (empty($_POST['phone']) || !preg_match('/^\+7\d{10}$/', $_POST['phone'])) {
    echo '<p style="color: red;">Заполните корректно телефон (формат: +7XXXXXXXXXX).</p>';
    $errors = true;
}

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo '<p style="color: red;">Заполните корректно email.</p>';
    $errors = true;
}

if (empty($_POST['birth_date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['birth_date'])) {
    echo '<p style="color: red;">Заполните корректно дату рождения (формат: YYYY-MM-DD).</p>';
    $errors = true;
}

if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
    echo '<p style="color: red;">Выберите пол.</p>';
    $errors = true;
}

if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
    echo '<p style="color: red;">Выберите хотя бы один язык программирования.</p>';
    $errors = true;
}

if (empty($_POST['biography']) || strlen($_POST['biography']) > 500) {
    echo '<p style="color: red;">Заполните биографию (не более 500 символов).</p>';
    $errors = true;
}

if (empty($_POST['contract_agreed'])) {
    echo '<p style="color: red;">Необходимо согласие с контрактом.</p>';
    $errors = true;
}

if ($errors) {
    exit();
}


$user = 'u68764';
$pass = '1980249';
$db = new PDO('mysql:host=localhost;dbname=u68764', $user, $pass, [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    $stmt = $db->prepare("INSERT INTO applications (full_name, phone, email, birth_date, gender, biography, contract_agreed) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['fio'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        $_POST['contract_agreed'] ? 1 : 0
    ]);
    $applications_id = $db->lastInsertId();

    foreach ($_POST['languages'] as $language_id) {
        $stmt = $db->prepare("INSERT INTO application_languages (applications_id, language_id) VALUES (?, ?)");
        $stmt->execute([$applications_id, $language_id]);
    }
} catch (PDOException $e) {
    echo '<p style="color: red;">Ошибка при сохранении данных: ' . $e->getMessage() . '</p>';
    exit();
}

header('Location: ?save=1');
?>
