<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <div class="form-container">
            <form method="post" action="index.php">
                <div class="form-group">
                    <label for="fio">ФИО:</label>
                    <input type="text" id="fio" name="fio" required pattern="[A-Za-zА-Яа-я\s]{1,150}" maxlength="150">
                </div>

                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="tel" id="phone" name="phone" required pattern="\+7\d{10}">
                </div>

                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="birth_date">Дата рождения:</label>
                    <input type="date" id="birth_date" name="birth_date" required>
                </div>

                <div class="form-group">
                    <label>Пол:</label>
                    <div class="radio-group">
                        <input type="radio" id="male" name="gender" value="male" required>
                        <label for="male">Мужской</label>
                        <input type="radio" id="female" name="gender" value="female" required>
                        <label for="female">Женский</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="languages">Любимый язык программирования:</label>
                    <select id="languages" name="languages[]" multiple required>
                        <option value="1">Pascal</option>
                        <option value="2">C</option>
                        <option value="3">C++</option>
                        <option value="4">JavaScript</option>
                        <option value="5">PHP</option>
                        <option value="6">Python</option>
                        <option value="7">Java</option>
                        <option value="8">Haskell</option>
                        <option value="9">Clojure</option>
                        <option value="10">Prolog</option>
                        <option value="11">Scala</option>
                        <option value="12">Go</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="biography">Биография:</label>
                    <textarea id="biography" name="biography" rows="4" required maxlength="500"></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="contract_agreed" name="contract_agreed" required>
                    <label for="contract_agreed">С контрактом ознакомлен(а)</label>
                </div>

                <button type="submit">Сохранить</button>
            </form>
        </div>
    </main>
</body>
</html>
