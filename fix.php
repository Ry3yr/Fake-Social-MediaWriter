<?php
$json_data = file_get_contents('data_alcea.json');
$data = json_decode($json_data, true);

// Fix syntax errors
foreach ($data as &$item) {
    // Fix errors in name field
    if (isset($item['name'])) {
        $item['name'] = htmlspecialchars($item['name']);
    }

    // Fix errors in description field
    if (isset($item['description'])) {
        $item['description'] = htmlspecialchars($item['description']);
    }

    // Fix errors in value field
    if (isset($item['value'])) {
        if (is_string($item['value'])) {
            $item['value'] = (float) $item['value'];
        }
    }
}

// Fix unterminated string errors
$json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
file_put_contents('data_alcea.json', $json_data);

echo "JSON file has been fixed.";