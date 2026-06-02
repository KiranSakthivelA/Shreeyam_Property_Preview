<?php
/**
 * Configuration File
 * Update these values with your actual database and Syncr CRM credentials.
 */

// MySQL Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');

// Syncr CRM Integration Details
define('SYNCR_API_URL', 'https://api.syncr.com/webhook/placeholder');
define('SYNCR_API_KEY', 'your_api_key_here');

// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access to this file is not allowed.');
}
?>
