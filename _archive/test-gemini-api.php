<?php
// Test script to verify Gemini API connection
require 'config.php';

echo "Testing Gemini API...\n\n";

// Check if cURL is enabled
if (!function_exists('curl_init')) {
    die("ERROR: cURL is not enabled in PHP!\n");
}

echo "✓ cURL is enabled\n";
echo "✓ API Key: " . substr(GEMINI_API_KEY, 0, 10) . "...\n";
echo "✓ Endpoint: " . GEMINI_API_ENDPOINT . "\n\n";

// Test API call
$apiUrl = GEMINI_API_ENDPOINT . '?key=' . GEMINI_API_KEY;

$requestBody = json_encode([
    'contents' => [
        [
            'parts' => [
                ['text' => 'Say hello']
            ]
        ]
    ]
]);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

echo "Sending request to Gemini API...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    die("ERROR: " . $curlError . "\n");
}

echo "HTTP Code: $httpCode\n";

if ($httpCode !== 200) {
    echo "ERROR Response:\n";
    echo $response . "\n";
    die();
}

$data = json_decode($response, true);
$aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';

echo "\n✓ SUCCESS!\n";
echo "AI Response: " . $aiResponse . "\n";
?>
