<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.php');
    exit();
}


$errors = FALSE;


if (empty($_POST['fio']) || !preg_match('/^[A-Za-zА-Яа-я\s]{1,150}$/u', $_POST['fio'])) {
    print('Заполните корректно ФИО (только буквы и пробелы, не более 150 символов).<br/>');
    $errors = TRUE;
}

if (empty($_POST['phone']) || !preg_match('/^\+7\d{10}$/', $_POST['phone'])) {
    print('Заполните корректно телефон (формат: +7XXXXXXXXXX).<br/>');
    $errors = TRUE;
}

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    print('Заполните корректно email.<br/>');
    $errors = TRUE;
}

if (empty($_POST['birth_date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['birth_date'])) {
    print('Заполните корректно дату рождения (формат: YYYY-MM-DD).<br/>');
    $errors = TRUE;
}

if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
    print('Выберите пол.<br/>');
    $errors = TRUE;
}

if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
    print('Выберите хотя бы один язык программирования.<br/>');
    $errors = TRUE;
}

if (empty($_POST['biography']) || strlen($_POST['biography']) > 500) {
    print('Заполните биографию (не более 500 символов).<br/>');
    $errors = TRUE;
}

if (empty($_POST['contract_agreed'])) {
    print('Необходимо согласие с контрактом.<br/>');
    $errors = TRUE;
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
    $stmt = $db->prepare("INSERT INTO application (full_name, phone, email, birth_date, gender, biography, contract_agreed) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['fio'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        $_POST['contract_agreed'] ? 1 : 0
    ]);
    $application_id = $db->lastInsertId();


    foreach ($_POST['languages'] as $language_id) {
        $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
        $stmt->execute([$application_id, $language_id]);
    }
} catch (PDOException $e) {
    print('Ошибка при сохранении данных: ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');
?>
