<?php

// Twitch channel name
$channelName = $_GET["canal"];

// Twitch client ID and client secret
$clientID = '';
$clientSecret = '';

// Get OAuth token
$tokenURL = 'https://id.twitch.tv/oauth2/token';
$tokenData = [
    'client_id' => $clientID,
    'client_secret' => $clientSecret,
    'grant_type' => 'client_credentials'
];
$tokenOptions = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($tokenData)
    ]
];
$tokenContext = stream_context_create($tokenOptions);
$tokenResponse = file_get_contents($tokenURL, false, $tokenContext);
$tokenJSON = json_decode($tokenResponse, true);

// Extract OAuth token
$accessToken = $tokenJSON['access_token'];

// API endpoint
$url = 'https://api.twitch.tv/helix/streams?user_login=' . $channelName;

// Initialize curl session
$ch = curl_init();

// Set curl options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Client-ID: ' . $clientID
]);

// Execute curl request
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo 'Error: ' . curl_error($ch);
    die();
}

// Close curl session
curl_close($ch);

// Decode JSON response
$data = json_decode($response, true);

// Check if stream is online
if (!empty($data['data'])) {
    echo 'Channel is online!';
    // Additional information about the stream
    $streamData = $data['data'][0];
    echo '<br>Title: ' . $streamData['title'] . '<br>';
    echo '<br>Viewer count: ' . $streamData['viewer_count'] . '<br>';
} else {
    echo 'Channel is offline.';
}
