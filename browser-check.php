<?php
// Check if cookies are enabled
if (!isset($_COOKIE['cookie_test']) && !isset($_POST['cookie_check'])) {
    setcookie('cookie_test', '1', time() + 3600, '/');
}

// Check for browser compatibility
function isBrowserCompatible() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    // Check for very old browsers
    if (preg_match('/MSIE [1-8]\./', $user_agent)) {
        return false;
    }
    
    // Check for other old browsers
    if (preg_match('/Firefox\/[1-20]\./', $user_agent)) {
        return false;
    }
    
    if (preg_match('/Chrome\/[1-30]\./', $user_agent)) {
        return false;
    }
    
    return true;
}

// Redirect to compatibility page if browser is not compatible
if (!isBrowserCompatible() && basename($_SERVER['PHP_SELF']) !== 'browser-compatibility.html') {
    header('Location: browser-compatibility.html');
    exit;
}

// Set SameSite attribute for cookies
ini_set('session.cookie_samesite', 'Lax');
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'samesite' => 'Lax',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true
    ]);
}
?>

