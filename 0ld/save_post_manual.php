<?php
// PHP logic runs before HTML is sent to the browser
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['json'])) {
    $file = 'data_akkoma.json';
    $incoming = json_decode($_POST['json'], true);

    if (!is_array($incoming)) {
        $saveMessage = "❌ Invalid JSON.";
    } else {
        // Load existing data
        $existing = [];
        if (file_exists($file)) {
            $existingData = file_get_contents($file);
            $existing = json_decode($existingData, true);
            if (!is_array($existing)) {
                $existing = [];
            }
        }

        // Prepend new entry
        $newData = array_merge($incoming, $existing);

        // Save to file
        file_put_contents($file, json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $saveMessage = "✅ Saved to data_akkoma.json";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>JSON Generator</title>
</head>
<body>
  <h2>Generate JSON Entry</h2>

  <?php if (!empty($saveMessage)): ?>
    <p><strong><?php echo htmlspecialchars($saveMessage); ?></strong></p>
  <?php endif; ?>

  <label for="date">Date:</label><br>
  <input type="text" id="date" size="40"><br><br>

  <label for="url">URL:</label><br>
  <input type="text" id="url" size="40" placeholder="https://example.com/notice/xyz"><br><br>

  <label for="preview">Preview:</label><br>
  <input type="text" id="preview" size="40" placeholder="Your preview text"><br><br>

  <button onclick="generateJSON()">Generate JSON</button>

  <h3>Output:</h3>
  <pre id="output"></pre>

  <!-- Save form below, reads from the JSON output -->
  <form method="POST" onsubmit="return submitJSON();">
    <input type="hidden" name="json" id="jsonField">
    <input type="submit" value="Save JSON to File" disabled>
  </form>

  <script>
    // Prefill the date field with the current ISO timestamp
    document.getElementById('date').value = new Date().toISOString();

    function generateJSON() {
      const date = document.getElementById('date').value;
      const url = document.getElementById('url').value;
      const preview = document.getElementById('preview').value;

      const result = [
        {
          date,
          url,
          preview
        }
      ];

      document.getElementById('output').textContent = JSON.stringify(result, null, 2);
    }

    function submitJSON() {
      const json = document.getElementById('output').textContent;
      if (!json.trim()) {
        alert("Please generate JSON first.");
        return false;
      }
      document.getElementById('jsonField').value = json;
      return true;
    }
  </script>
</body>
</html>
