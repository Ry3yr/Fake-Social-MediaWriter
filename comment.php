<?php
if (file_exists('comments.json')) {
    $existingData = json_decode(file_get_contents('comments.json'), true);
} else {
    $existingData = array();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'];
    $value = $_POST['value'];
    array_unshift($existingData, array(
        'text' => $text,
        'value' => $value
    ));
    $jsonData = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents('comments.json', $jsonData);
}
?>
    <h2>Add a Comment</h2>
    <form method="post">
        <label for="text">Target:</label>
        <input type="text" id="text" name="text" value="<?php echo isset($_GET['text']) ? $_GET['text'] : ''; ?>">
        <br>
        <label for="value">Value:</label>
        <textarea id="value" name="value" cols="25" rows="15"></textarea>
        <br>
        <button type="submit">Submit</button>
    </form>
    <!---<script>
        document.getElementById('textDisplay').textContent = '<?php echo isset($_GET['text']) ? $_GET['text'] : 'not set'; ?>';
    </script>-->