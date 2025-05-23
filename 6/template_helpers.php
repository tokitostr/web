<?php
function renderTable(array $data, array $columns, array $actions = []): string {
    $html = '<table><thead><tr>';
    
    foreach ($columns as $column) {
        $html .= '<th>' . htmlspecialchars($column['title']) . '</th>';
    }
    
    if (!empty($actions)) {
        $html .= '<th>Действия</th>';
    }
    
    $html .= '</tr></thead><tbody>';
    
    foreach ($data as $row) {
        $html .= '<tr>';
        
        foreach ($columns as $key => $column) {
            $value = $row[$key] ?? '';
            
            if (isset($column['formatter'])) {
                $value = $column['formatter']($value);
            } else {
                $value = htmlspecialchars($value);
            }
            
            $html .= '<td>' . $value . '</td>';
        }
        
        if (!empty($actions)) {
            $html .= '<td>';
            foreach ($actions as $action) {
                $html .= '<form method="POST" style="display:inline;">
                    <button type="submit" name="' . $action['name'] . '" value="' . $row['id'] . '">' . 
                    htmlspecialchars($action['title']) . '</button>
                </form>';
            }
            $html .= '</td>';
        }
        
        $html .= '</tr>';
    }
    
    return $html . '</tbody></table>';
}

function renderFormField(string $type, string $name, string $label, array $errors = [], array $values = [], array $attrs = []): string {
    if (!is_array($errors)) {
        $errors = [];
    }
    
    $html = '<label>'.htmlspecialchars($label).':</label>';
    $html .= '<input type="'.htmlspecialchars($type).'" name="'.htmlspecialchars($name).'" ';
    
    foreach ($attrs as $attr => $val) {
        $html .= htmlspecialchars($attr).'="'.htmlspecialchars($val).'" ';
    }
    
    if (!empty($errors[$name])) {
        $html .= 'class="error" ';
    }
    
    $html .= 'value="'.htmlspecialchars($values[$name] ?? '').'">';
    
    if (!empty($errors[$name])) {
        $html .= '<div class="error-message">'.htmlspecialchars($errors[$name]).'</div>';
    }
    
    return $html;
}

function renderRadioField(string $name, string $value, string $label, array $values = []): string {
    $checked = ($values[$name] ?? '') === $value ? 'checked' : '';
    return '<input type="radio" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" ' . $checked . '> ' . htmlspecialchars($label);
}

function renderTextarea(string $name, string $label, array $errors = [], array $values = [], array $attrs = []): string {
    $html = '<label>' . htmlspecialchars($label) . ':</label>';
    $html .= '<textarea name="' . htmlspecialchars($name) . '" ';
    
    if (!empty($attrs)) {
        foreach ($attrs as $attr => $val) {
            $html .= htmlspecialchars($attr) . '="' . htmlspecialchars($val) . '" ';
        }
    }
    
    if (!empty($errors[$name])) {
        $html .= 'class="error"';
    }
    
    $html .= '>' . htmlspecialchars($values[$name] ?? '') . '</textarea>';
    
    if (!empty($errors[$name])) {
        $html .= '<div class="error-message">' . htmlspecialchars($errors[$name]) . '</div>';
    }
    
    return $html;
}

function renderSelectLanguages(array $selected = []): string {
    $db = new DatabaseRepository();
    $languages = $db->getAllLanguages();
    
    $html = '<select name="languages[]" multiple required>';
    foreach ($languages as $lang) {
        $selectedAttr = in_array($lang['id'], $selected) ? 'selected' : '';
        $html .= '<option value="' . $lang['id'] . '" ' . $selectedAttr . '>' . 
                 htmlspecialchars($lang['language_name']) . '</option>';
    }
    return $html . '</select>';
}

function truncateText(string $text, int $length): string {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}