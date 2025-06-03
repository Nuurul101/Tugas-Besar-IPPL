<?php
// Security Constants
define('SECURE_SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'BANYUMA_SPORT_SESSION');

// Set secure session parameters
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', SECURE_SESSION_LIFETIME);
ini_set('session.gc_maxlifetime', SECURE_SESSION_LIFETIME);
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '');
ini_set('session.name', SESSION_NAME);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 