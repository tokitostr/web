<?php
echo '<link rel="stylesheet" href="styles.css">';
?>

<main>
  <div class="change">
    <div id="form">
      <form method="post" action="index.php">
        <label>ФИО:</label>
        <input type="text" name="fio" required pattern="[A-Za-zА-Яа-я\s]{1,150}" maxlength="150"><br>

        <label>Телефон:</label>
        <input type="tel" name="phone" required pattern="\+7\d{10}"><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Дата рождения:</label>
        <input type="date" name="birth_date" required><br>

        <label>Пол:</label>
        <input type="radio" name="gender" value="male" required> Мужской
        <input type="radio" name="gender" value="female"> Женский<br>

        <label>Любимый язык программирования:</label>
        <select name="languages[]" multiple required>
          <option value="1">Pascal</option>
          <option value="2">C</option>
          <option value="3">C++</option>
          <option value="4">JavaScript</option>
          <option value="5">PHP</option>
          <option value="6">Python</option>
          <option value="7">Java</option>
          <option value="8">Haskel</option>
          <option value="9">Clojure</option>
          <option value="10">Prolog</option>
          <option value="11">Scala</option>
          <option value="12">Go</option>
        </select><br>

        <label>Биография:</label>
        <textarea name="biography" required maxlength="500"></textarea><br>

        <label>
          <input type="checkbox" name="contract_agreed" required> С контрактом ознакомлен(а)
        </label><br>

        <input type="submit" value="Сохранить">
      </form>
    </div>
  </div>
</main>
