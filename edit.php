<?php
echo '<style>
  .container { display: flex; gap: 10px; }
  .container > div {
    width: 45%;
    height: 300px;
    box-sizing: border-box;
  }
  .original-box {
    border: 1px solid #ccc;
    padding: 10px;
    white-space: pre-wrap;
    overflow-y: auto;
    font-family: monospace;
    background: #fff;
  }
  textarea {
    width: 45%;
    height: 300px;
    box-sizing: border-box;
    font-family: monospace;
  }
</style>';
// Path to your JSON file
$jsonFile = __DIR__ . '/data_alcea.json';

// Helper function to highlight differences (simple version)
function highlightDiff($old, $new) {
    $oldWords = preg_split('/(\s+)/', $old, -1, PREG_SPLIT_DELIM_CAPTURE);
    $newWords = preg_split('/(\s+)/', $new, -1, PREG_SPLIT_DELIM_CAPTURE);

    $result = '';
    $i = 0; $j = 0;

    while ($i < count($oldWords) && $j < count($newWords)) {
        if ($oldWords[$i] === $newWords[$j]) {
            $result .= htmlspecialchars($oldWords[$i]);
            $i++; $j++;
        } else {
            // Mark old words missing in new as bold
            $result .= '<b>' . htmlspecialchars($oldWords[$i]) . '</b>';
            $i++;
        }
    }
    // Remaining old words
    while ($i < count($oldWords)) {
        $result .= '<b>' . htmlspecialchars($oldWords[$i]) . '</b>';
        $i++;
    }
    // Note: This only highlights removed text on left side for simplicity
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_text'], $_POST['original_text'])) {
    // Save changes

    $newText = $_POST['new_text'];
    $originalText = $_POST['original_text'];

    $json = file_get_contents($jsonFile);
    $data = json_decode($json, true);

    if (!is_array($data)) {
        die('Invalid JSON data.');
    }

    // Find and update the matching entry by original text
    foreach ($data as &$item) {
        foreach ($item as $date => &$entry) {
            if (isset($entry['value']) && $entry['value'] === $originalText) {
                $entry['value'] = $newText;
                // Save JSON
                file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                echo "<p>Entry updated successfully.</p>";
                echo "<p><a href='/other/extra/scripts/fakesocialmedia/data_alcea.json'>JSON</a></p>";
                exit;
            }
        }
    }
    echo "<p>Original entry not found in JSON.</p>";
    exit;
}

// Display diff and form when GET params present
if (isset($_GET['new'], $_GET['original'])) {
    $new = $_GET['new'];
    $original = $_GET['original'];

    echo '<style>textarea { width: 45%; height: 300px; } .container { display: flex; gap: 10px; }</style>';
    echo '<h2>Edit Entry</h2>';

    // Show side-by-side with differences in bold on left side
    echo '<div class="container">';
    echo '<div><h3>Original (diff highlights)</h3><div style="border:1px solid #ccc; padding:10px; white-space: pre-wrap;">';
    echo highlightDiff($original, $new);
    echo '</div></div>';

    echo '<div><h3>New (edited)</h3>';
    echo '<form method="POST">';
    echo '<textarea name="new_text">' . htmlspecialchars($new) . '</textarea>';
    echo '<textarea name="original_text" style="display:none;">' . htmlspecialchars($original) . '</textarea><br>';
    echo '<button type="submit">Save changes</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';

} else {
    echo "<p>No edit data provided. Please provide ?new=...&original=... in the URL.</p>";
}
