<?php
// Simple test to check if Gemini API endpoint is working
session_start();

// Simulate a logged-in user for testing
$_SESSION['user_id'] = 1; // Temporary for testing

// Test the API
$url = 'http://localhost/Sheild/Spiderman/Weby_Vanilla/api/gemini.php';

$data = json_encode([
    'prompt' => 'Hello, this is a test'
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Cookie: ' . session_name() . '=' . session_id()
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n\n";
echo "Response:\n";
echo $response;
?>
