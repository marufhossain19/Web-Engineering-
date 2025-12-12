<?php
// ========================================
// Gemini AI API Endpoint - Secure Backend
// ========================================

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in HTML
ini_set('log_errors', 1);

// Set JSON response header FIRST to ensure we always return JSON
header('Content-Type: application/json');

// Start output buffering to catch any accidental output
ob_start();

session_start();

// Require config file with correct path
try {
    require_once __DIR__ . '/../config.php';
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Configuration error: ' . $e->getMessage()
    ]);
    exit;
}

// ========== Security: Check if user is logged in ==========
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication required. Please log in to use Gemini AI.'
    ]);
    exit;
}

// ========== Rate Limiting ==========
// Initialize rate limit tracking in session
if (!isset($_SESSION['gemini_requests'])) {
    $_SESSION['gemini_requests'] = [];
}

// Clean up old requests (older than 1 minute)
$currentTime = time();
$_SESSION['gemini_requests'] = array_filter(
    $_SESSION['gemini_requests'],
    function($timestamp) use ($currentTime) {
        return ($currentTime - $timestamp) < 60; // Keep requests from last 60 seconds
    }
);

// Check rate limit (10 requests per minute)
$requestLimit = 10;
if (count($_SESSION['gemini_requests']) >= $requestLimit) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'error' => 'Rate limit exceeded. Please wait a moment before trying again.'
    ]);
    exit;
}

// ========== Validate Input ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['prompt']) || empty(trim($input['prompt']))) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Prompt is required.'
    ]);
    exit;
}

$prompt = trim($input['prompt']);

// Sanitize prompt (limit length to prevent abuse)
if (strlen($prompt) > 5000) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Prompt is too long. Maximum 5000 characters.'
    ]);
    exit;
}

// ========== Call Gemini API ==========
try {
    // Add current request to rate limit tracker
    $_SESSION['gemini_requests'][] = $currentTime;
    
    // Prepare API request
    $apiUrl = GEMINI_API_ENDPOINT . '?key=' . GEMINI_API_KEY;
    
    $requestBody = json_encode([
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ]);
    
    // Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 second timeout
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Check for cURL errors
    if ($curlError) {
        throw new Exception('Network error: ' . $curlError);
    }
    
    // Check HTTP response code
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMessage = $errorData['error']['message'] ?? 'Unknown API error';
        throw new Exception('Gemini API error: ' . $errorMessage);
    }
    
    // Parse response
    $data = json_decode($response, true);
    
    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception('Invalid response format from Gemini API');
    }
    
    $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
    
    // Return success response
    echo json_encode([
        'success' => true,
        'response' => $aiResponse
    ]);
    
} catch (Exception $e) {
    // Log error (in production, use proper logging)
    error_log('Gemini API Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Sorry, something went wrong. Please try again later.',
        'debug' => $e->getMessage() // Temporary for debugging
    ]);
}
?>
