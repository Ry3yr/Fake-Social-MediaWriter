<?php
// Get the URL from the query string
$url = isset($_GET['url']) ? $_GET['url'] : '';

if (empty($url) || strpos($url, 'bandcamp.com/track/') === false) {
    die("Please provide a valid Bandcamp track URL using ?url=");
}

// 1. Fetch the HTML content
// We use a stream context with a User-Agent to prevent 403 Forbidden errors
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36\r\n"
    ]
];
$context = stream_context_create($options);
$html = @file_get_contents($url, false, $context);

if ($html === FALSE) {
    die("Could not retrieve the page content.");
}

// 2. Extract the content URL from the <meta property="og:video"> tag
// We look for: <meta property="og:video" content="...">
if (preg_match('/property="og:video"\s+content="([^"]+)"/', $html, $matches)) {
    $embedded_url = $matches[1];
    
    // 3. Parse the Track ID from that URL
    // The URL looks like: https://bandcamp.com/EmbeddedPlayer/v=2/track=3118106658/...
    preg_match('/track=(\d+)/', $embedded_url, $track_matches);
    $track_id = $track_matches[1] ?? null;

    // Try to find Album ID if it exists in the string (it might not be in the og:video tag for singles)
    preg_match('/album=(\d+)/', $embedded_url, $album_matches);
    $album_id = $album_matches[1] ?? null;

    // Get the page title for the link text
    preg_match('/<title>(.*?)<\/title>/', $html, $title_matches);
    $title = $title_matches[1] ?? 'Bandcamp Track';

    if ($track_id) {
        // 4. Construct the final Iframe
        // We build the src string exactly as you requested. 
        // Note: If album_id was not found, we simply omit it, and the player will still work.
        
        $src_parts = [];
        if ($album_id) $src_parts[] = "album={$album_id}";
        $src_parts[] = "size=large";
        $src_parts[] = "bgcol=ffffff";
        $src_parts[] = "linkcol=0687f5";
        $src_parts[] = "tracklist=false";
        $src_parts[] = "artwork=small";
        $src_parts[] = "track={$track_id}";
        $src_parts[] = "transparent=true";
        
        $final_src = "https://bandcamp.com/EmbeddedPlayer/" . implode('/', $src_parts) . "/";

        echo <<<HTML
<iframe style="border: 0; width: 100%; height: 120px;" 
        src="{$final_src}" 
        seamless>
    <a href="{$url}">{$title}</a>
</iframe>
HTML;
    } else {
        echo "Found the meta tag, but could not extract a Track ID from it.";
    }

} else {
    echo "Could not find the 'og:video' meta tag in the page source.";
}
?>