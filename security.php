<?php
/**
 * Security Module - Session Hijacking Protection
 * Prevents unauthorized session access by validating User Agent and IP Address
 */

// Configuration
define('ENABLE_IP_VALIDATION', false); // Set to true for high-security, false for mobile-friendly
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds

/**
 * Initialize session security fingerprints
 * Call this after successful login/registration
 */
function init_session_security() {
    // Store User Agent (browser fingerprint)
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Store IP Address
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Store session start time
    $_SESSION['session_start'] = time();
    
    // Store last activity time
    $_SESSION['last_activity'] = time();
}

/**
 * Validate session to prevent hijacking
 * Call this at the start of every protected page
 */
function validate_session() {
    // Skip validation if user is not logged in
    if (!isset($_SESSION['user_id'])) {
        return;
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        if ($inactive_time > SESSION_TIMEOUT) {
            session_destroy();
            header("Location: login.php?error=session_timeout");
            exit;
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Validate User Agent
    if (isset($_SESSION['user_agent'])) {
        $current_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($current_agent !== $_SESSION['user_agent']) {
            // User Agent mismatch - possible session hijacking
            session_destroy();
            header("Location: login.php?error=session_hijacked");
            exit;
        }
    }
    
    // Validate IP Address (if enabled)
    if (ENABLE_IP_VALIDATION && isset($_SESSION['user_ip'])) {
        $current_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if ($current_ip !== $_SESSION['user_ip']) {
            // IP Address mismatch - possible session hijacking
            session_destroy();
            header("Location: login.php?error=session_hijacked");
            exit;
        }
    }
}

/**
 * Regenerate session ID for security
 * Call this after privilege escalation or sensitive operations
 */
function regenerate_session() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
        // Re-initialize security fingerprints
        init_session_security();
    }
}

/**
 * Get session info for debugging (admin only)
 */
function get_session_info() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'user_agent' => $_SESSION['user_agent'] ?? 'Not set',
        'user_ip' => $_SESSION['user_ip'] ?? 'Not set',
        'session_start' => isset($_SESSION['session_start']) ? date('Y-m-d H:i:s', $_SESSION['session_start']) : 'Not set',
        'last_activity' => isset($_SESSION['last_activity']) ? date('Y-m-d H:i:s', $_SESSION['last_activity']) : 'Not set',
        'time_remaining' => isset($_SESSION['last_activity']) ? (SESSION_TIMEOUT - (time() - $_SESSION['last_activity'])) : 0
    ];
}
?>
