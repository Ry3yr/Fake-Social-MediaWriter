<?php
$url = "https://alcea-wisteria.de/z_files/emoji/";
$html = file_get_contents($url);
preg_match_all('/<a href=[\'"](.*?\.gif)[\'"]/i', $html, $matches);
$gifUrls = $matches[1];
$gridSize = 5; // Number of columns in the grid
$count = 0;
echo "<table>";
echo "<tr>";
foreach ($gifUrls as $gifUrl) {
    $filename = basename($gifUrl);
    echo '<td><a href="javascript:void(0);" onclick="insertEmoji(\'' . $filename . '\');"><img src="https://alcea-wisteria.de/z_files/emoji/' . $filename . '" width=30></a></td>';
    $count++;
    if ($count % $gridSize == 0) {
        echo "</tr><tr>";
    }
}
echo "</tr>";
echo "</table>";
?>
<script>
function insertEmoji(emoji) {
    var textarea = document.getElementById('textarea');
    textarea.value += ':' + emoji.split('.')[0] + ':';
}
</script>