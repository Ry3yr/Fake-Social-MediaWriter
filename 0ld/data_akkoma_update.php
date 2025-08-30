<?php
$jsonPath = $_SERVER['DOCUMENT_ROOT'] . '/other/extra/scripts/fakesocialmedia/0ld/data_akkoma.json';

if (!file_exists($jsonPath)) {
    die("JSON file not found at: $jsonPath");
}

// Read and decode JSON
$data = json_decode(file_get_contents($jsonPath), true);
if (!is_array($data)) {
    die("Invalid JSON format.");
}

// Find the latest entry by 'date'
usort($data, function ($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
$latest = &$data[0];  // Use reference so we can modify it directly

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_url'])) {
    $newUrl = trim($_POST['new_url']);
    if (filter_var($newUrl, FILTER_VALIDATE_URL)) {
        $latest['url'] = $newUrl;

        // Save updated data
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $message = "<p style='color: green;'>✅ URL updated successfully.</p>";
    } else {
        $message = "<p style='color: red;'>❌ Invalid URL format.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Latest Entry</title>
    <style>
        .preview-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            background: #f9f9f9;
            font-family: sans-serif;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
        }
    </style>
</head>
<body>
    <h2>Latest Entry (Date: <?= htmlspecialchars($latest['date']) ?>)</h2>

    <?php if ($message) echo $message; ?>

    <div class="preview-box">
        <strong>Preview:</strong><br>
        <?= $latest['mode'] === 'html' ? $latest['preview'] : htmlspecialchars($latest['preview']) ?>
    </div>

    <form method="post">
        <label for="new_url">Edit URL:</label><br>
        <input type="text" name="new_url" id="new_url" value="<?= htmlspecialchars($latest['url']) ?>"><br><br>
        <input type="submit" value="Update URL">
    </form>
</body>
</html>
