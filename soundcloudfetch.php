<?php

// Check if the 'scurl' query parameter is provided
if (isset($_GET['scurl'])) {
    $url = $_GET['scurl'];  // Get the SoundCloud URL from the query string
} else {
    die('Error: No SoundCloud URL provided.');
}

// Initialize a cURL session to get the HTML content of the page
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);

// Check if the HTML was fetched successfully
if (!$html) {
    die('Error fetching the SoundCloud page.');
}

// Load the HTML content into DOMDocument
$doc = new DOMDocument();
libxml_use_internal_errors(true);  // Suppress errors for malformed HTML
$doc->loadHTML($html);
libxml_clear_errors();

// Use DOMXPath to query the page content
$xpath = new DOMXPath($doc);

// Look for the <meta> tag containing the SoundCloud track ID
$metaTags = $xpath->query("//meta[contains(@content, 'soundcloud://sounds:')]");

if ($metaTags->length > 0) {
    // Extract the content attribute from the meta tag
    $content = $metaTags->item(0)->getAttribute('content');
    
    // Extract the track ID using a regular expression
    if (preg_match('/soundcloud:\/\/sounds:(\d+)/', $content, $matches)) {
        $track_id = $matches[1];
        
        // Build the iframe HTML
        $iframe = '<iframe width="100%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' . $track_id . '&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>';
        
        // Get the artist name and track title from the meta tags
        $artistMeta = $xpath->query("//meta[@property='og:site_name']");
        $trackMeta = $xpath->query("//meta[@property='og:title']");
        
        $artist_name = "Unknown Artist";
        $track_title = "Unknown Track";
        
        if ($artistMeta->length > 0) {
            $artist_name = $artistMeta->item(0)->getAttribute('content');
        }
        
        if ($trackMeta->length > 0) {
            $track_title = $trackMeta->item(0)->getAttribute('content');
        }

        // Build the song details HTML
        $details = '<div style="font-size: 10px; color: #cccccc; line-break: anywhere; word-break: normal; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-family: Interstate, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Garuda, Verdana, Tahoma, sans-serif; font-weight: 100;">';
        $details .= '<a href="https://soundcloud.com/' . strtolower(str_replace(' ', '-', $artist_name)) . '" title="' . htmlspecialchars($artist_name) . '" target="_blank" style="color: #cccccc; text-decoration: none;">' . htmlspecialchars($artist_name) . '</a> · ';
        $details .= '<a href="https://soundcloud.com/' . strtolower(str_replace(' ', '-', $artist_name)) . '/' . strtolower(str_replace(' ', '-', $track_title)) . '" title="' . htmlspecialchars($track_title) . '" target="_blank" style="color: #cccccc; text-decoration: none;">' . htmlspecialchars($track_title) . '</a>';
        $details .= '</div>';

        // Output the iframe and details
        echo $iframe;
        echo $details;
        
    } else {
        echo "Track ID not found in the meta tag.\n";
    }
} else {
    echo "No meta tag containing track data found.\n";
}

?>
