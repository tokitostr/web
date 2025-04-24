<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = [];
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = 'Спасибо, результаты сохранены.';
        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass']));
        }
    }

    $errors = [];
    $error_fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract_agreed'];
    foreach ($error_fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field . '_error']) ? $_COOKIE[$field . '_error'] : '';
        setcookie($field . '_error', '', time() - 3600);
    }

    $values = [];
    foreach ($error_fields as $field) {
        $values[$field] = empty($_COOKIE[$field . '_value']) ? '' : $_COOKIE[$field . '_value'];
    }
    $values['languages'] = empty($_COOKIE['languages_value']) ? [] : json_decode($_COOKIE['languages_value'], true);
    $values['contract_agreed'] = !empty($_COOKIE['contract_agreed_value']);

    if (empty($errors) && !empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
        $user = 'u42690';
        $pass = '7522024';
        $db = new PDO('mysql:host=localhost;dbname=u42690', $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $db->prepare("SELECT * FROM application WHERE login = ?");
        $stmt->execute([$_SESSION['login']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $values = [
                'fio' => $user_data['full_name'],
                'phone' => $user_data['phone'],
                'email' => $user_data['email'],
                'birth_date' => $user_data['birth_date'],
                'gender' => $user_data['gender'],
                'biography' => $user_data['biography'],
                'contract_agreed' => $user_data['contract_agreed']
            ];

            $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
            $stmt->execute([$user_data['id']]);
            $values['languages'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }

    $_SESSION['errors'] = $errors;
    $_SESSION['values'] = $values;
    $_SESSION['messages'] = $messages;

    include('form.php');
    exit();
}

$errors = [];
$messages = [];

if (empty($_POST['fio']) || !preg_match('/^[A-Za-zА-Яа-я\s]{1,150}$/u', $_POST['fio'])) {
    $errors['fio'] = 'Заполните корректно ФИО (только буквы и пробелы, не более 150 символов).';
    setcookie('fio_error', $errors['fio'], time() + 24 * 60 * 60);
}
setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['phone']) || !preg_match('/^\+7\d{10}$/', $_POST['phone'])) {
    $errors['phone'] = 'Заполните корректно телефон (формат: +7XXXXXXXXXX).';
    setcookie('phone_error', $errors['phone'], time() + 24 * 60 * 60);
}
setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Заполните корректно email.';
    setcookie('email_error', $errors['email'], time() + 24 * 60 * 60);
}
setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['birth_date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['birth_date'])) {
    $errors['birth_date'] = 'Заполните корректно дату рождения (формат: YYYY-MM-DD).';
    setcookie('birth_date_error', $errors['birth_date'], time() + 24 * 60 * 60);
}
setcookie('birth_date_value', $_POST['birth_date'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
    $errors['gender'] = 'Выберите пол.';
    setcookie('gender_error', $errors['gender'], time() + 24 * 60 * 60);
}
setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
    $errors['languages'] = 'Выберите хотя бы один язык программирования.';
    setcookie('languages_error', $errors['languages'], time() + 24 * 60 * 60);
}
setcookie('languages_value', json_encode($_POST['languages']), time() + 30 * 24 * 60 * 60);

if (empty(trim($_POST['biography'])) || strlen($_POST['biography']) > 500) {
    $errors['biography'] = 'Заполните биографию (не более 500 символов).';
    setcookie('biography_error', $errors['biography'], time() + 24 * 60 * 60);
}
setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 60 * 60);

if (empty($_POST['contract_agreed'])) {
    $errors['contract_agreed'] = 'Необходимо согласие с контрактом.';
    setcookie('contract_agreed_error', $errors['contract_agreed'], time() + 24 * 60 * 60);
}
setcookie('contract_agreed_value', $_POST['contract_agreed'], time() + 30 * 24 * 60 * 60);

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['values'] = $_POST;
    header('Location: index.php');
    exit();
}

$user = 'u42690';
$pass = '7522024';
$db = new PDO('mysql:host=localhost;dbname=u42690', $user, $pass, [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

try {
    if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
        $stmt = $db->prepare("UPDATE application SET full_name = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ?, contract_agreed = ? WHERE login = ?");
        $stmt->execute([
            $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birth_date'], $_POST['gender'], $_POST['biography'], $_POST['contract_agreed'] ? 1 : 0, $_SESSION['login']
        ]);

        $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
        $stmt->execute([$_SESSION['uid']]);

        foreach ($_POST['languages'] as $language_id) {
            $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['uid'], $language_id]);
        }
    } else {
        $login = uniqid();
        $pass = substr(md5(rand()), 0, 8);
        $pass_hash = md5($pass);

        $stmt = $db->prepare("INSERT INTO application (full_name, phone, email, birth_date, gender, biography, contract_agreed, login, pass) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birth_date'], $_POST['gender'], $_POST['biography'], $_POST['contract_agreed'] ? 1 : 0, $login, $pass_hash
        ]);
        $application_id = $db->lastInsertId();

        foreach ($_POST['languages'] as $language_id) {
            $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            $stmt->execute([$application_id, $language_id]);
        }

        setcookie('login', $login);
        setcookie('pass', $pass);
    }
} catch (PDOException $e) {
    print('Ошибка при сохранении данных: ' . $e->getMessage());
    exit();
}

setcookie('save', '1', time() + 24 * 60 * 60);
header('Location: index.php');
?>
