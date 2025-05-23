<?php
class DatabaseRepository {
    private $db;
    
    public function __construct() {
        $this->db = $this->getDBConnection();
    }
    
    private function getDBConnection() {
        static $db = null;
        if ($db === null) {
            $user = 'u68764';
            $pass = '1980249';
            $db = new PDO('mysql:host=localhost;dbname=u68596', $user, $pass, [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            $this->initDatabase($db);
        }
        return $db;
    }
    
    private function initDatabase($db) {
        $stmt = $db->query("SELECT COUNT(*) FROM admin_users");
        if ($stmt->fetchColumn() == 0) {
            $stmt = $db->prepare("INSERT INTO admin_users (login, password_hash) VALUES (?, ?)");
            $stmt->execute(['admin', md5('123')]);
        }
    }

    public function checkUserCredentials($login, $password) {
        $stmt = $this->db->prepare("SELECT id, pass FROM application WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        return ($user && md5($password) === $user['pass']) ? $user : null;
    }
    
    public function getUser($id) {
        $stmt = $this->db->prepare("SELECT * FROM application WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getUserLanguages($id) {
        $stmt = $this->db->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getAllLanguages() {
        $stmt = $this->db->query("SELECT id, language_name FROM programming_languages");
        return $stmt->fetchAll();
    }
    
    public function createUser($data) {
        $login = uniqid();
        $pass = substr(md5(rand()), 0, 8);
        $pass_hash = md5($pass);
        
        $stmt = $this->db->prepare("
            INSERT INTO application 
            (full_name, phone, email, birth_date, gender, biography, contract_agreed, login, pass) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['full_name'], 
            $data['phone'], 
            $data['email'], 
            $data['birth_date'], 
            $data['gender'], 
            $data['biography'], 
            $data['contract_agreed'] ? 1 : 0, 
            $login, 
            $pass_hash
        ]);
        
        $userId = $this->db->lastInsertId();
        $this->saveUserLanguages($userId, $data['languages']);
        
        return ['login' => $login, 'pass' => $pass];
    }
    
    public function updateUser($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE application SET 
            full_name=?, phone=?, email=?, 
            birth_date=?, gender=?, biography=?, 
            contract_agreed=? WHERE id=?
        ");
        $stmt->execute([
            $data['full_name'], 
            $data['phone'], 
            $data['email'], 
            $data['birth_date'], 
            $data['gender'], 
            $data['biography'], 
            $data['contract_agreed'] ? 1 : 0, 
            $id
        ]);
        
        $this->saveUserLanguages($id, $data['languages']);
        return true;
    }
    
    private function saveUserLanguages($userId, $languages) {
        $this->db->prepare("DELETE FROM application_languages WHERE application_id = ?")->execute([$userId]);
        foreach ($languages as $language_id) {
            $this->db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)")
               ->execute([$userId, $language_id]);
        }
    }
    
    public function getAllApplications() {
        $stmt = $this->db->prepare("
            SELECT a.*, GROUP_CONCAT(p.language_name SEPARATOR ', ') as languages_list 
            FROM application a
            LEFT JOIN application_languages al ON a.id = al.application_id
            LEFT JOIN programming_languages p ON al.language_id = p.id
            GROUP BY a.id
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getLanguageStatistics() {
        $stmt = $this->db->prepare("
            SELECT p.id, p.language_name, COUNT(al.application_id) as user_count 
            FROM programming_languages p
            LEFT JOIN application_languages al ON p.id = al.language_id
            GROUP BY p.id
            ORDER BY user_count DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function validateAdminCredentials() {
        if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="Admin Panel"');
            echo '<h1>401 Требуется авторизация</h1>';
            exit();
        }

        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE login = ?");
        $stmt->execute([$_SERVER['PHP_AUTH_USER']]);
        $admin = $stmt->fetch();

        if (!$admin || md5($_SERVER['PHP_AUTH_PW']) !== $admin['password_hash']) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="Admin Panel"');
            echo '<h1>401 Неверные учетные данные</h1>';
            exit();
        }
    }

    public function deleteUser($id) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("DELETE FROM application_languages WHERE application_id = ?");
            $stmt->execute([$id]);
            
            $stmt = $this->db->prepare("DELETE FROM application WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}
