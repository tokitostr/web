<?php
class Validator {
    public static function validateUserForm(array $data): array {
        $errors = [];
        
        if (empty($data['full_name']) || !preg_match('/^[A-Za-zА-Яа-я\s]{1,150}$/u', $data['full_name'])) {
            $errors['full_name'] = 'Заполните корректно ФИО (только буквы и пробелы, не более 150 символов).';
        }
        
        if (empty($data['phone']) || !preg_match('/^\+7\d{10}$/', $data['phone'])) {
            $errors['phone'] = 'Заполните корректно телефон (формат: +7XXXXXXXXXX).';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Заполните корректно email.';
        }
        
        if (empty($data['birth_date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birth_date'])) {
            $errors['birth_date'] = 'Заполните корректно дату рождения (формат: YYYY-MM-DD).';
        }
        
        if (empty($data['gender']) || !in_array($data['gender'], ['male', 'female'])) {
            $errors['gender'] = 'Выберите пол.';
        }
        
        if (empty($data['languages']) || !is_array($data['languages'])) {
            $errors['languages'] = 'Выберите хотя бы один язык программирования.';
        }
        
        if (empty(trim($data['biography'])) || strlen($data['biography']) > 500) {
            $errors['biography'] = 'Заполните биографию (не более 500 символов).';
        }
        
        if (empty($data['contract_agreed'])) {
            $errors['contract_agreed'] = 'Необходимо согласие с контрактом.';
        }
        
        return $errors;
    }
}