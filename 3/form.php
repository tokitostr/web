<?php

header('Content-Type: text/html; charset=UTF-8');

$host = 'localhost';
$username = 'u68764';
$password = '1980249';


$pdo = new PDO("mysql:host=$host;dbname=$username;charset=utf8", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fio = validate_input($_POST["fio"]);
    $phone = validate_input($_POST["phone"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ? $_POST["email"] : null;
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $bio = validate_input($_POST["bio"]);
    $contract = isset($_POST["contract"]) ? 1 : 0;
    $languages = isset($_POST["language"]) ? (array)$_POST["language"] : [];

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]{1,150}$/u", $fio)) {
        $errors[] = "ФИО может содержать только буквы и пробелы (до 150 символов)";
    }
    if (!$email) {
        $errors[] = "Некорректный e-mail";
    }
    if (!in_array($gender, ["male", "female"])) {
        $errors[] = "Некорректный пол";
    }
    if (empty($languages)) {
        $errors[] = "Выберите хотя бы один язык программирования";
    }

    if (empty($errors)) {
        try {
            //запись информации
            $stmt = $pdo->prepare("INSERT INTO applications (fio, phone, email, dob, gender, bio, contract) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fio, $phone, $email, $dob, $gender, $bio, $contract]);
            $application_id = $pdo->lastInsertId();

            //ID языков програм
            $stmtLang = $pdo->prepare("SELECT id FROM languages WHERE name = ?");
            $stmtInsert = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");

            foreach ($languages as $language) {
                //ID языка в таблице languages
                $stmtLang->execute([$language]);
                $lang_id = $stmtLang->fetchColumn();

                if ($lang_id) {
                    // вставка связки application_id language_id
                    $stmtInsert->execute([$application_id, $lang_id]);
                } else {
                    echo "<p style='color: red;'>Ошибка: Язык '$language' не найден в БД.</p>";
                }
            }

            echo "<p style='color: green;'>Данные успешно сохранены!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Ошибка БД: " . $e->getMessage() . "</p>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>

