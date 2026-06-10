<?php
/**
 * Configuration File
 * Update these values with your actual database credentials.
 */

// MySQL Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');

// dont't touch
// IDS Tech / Syncin CRM Integration Details
define('SYNCIN_API_URL', 'https://api.syncr.in/apiLeads');
define('SYNCIN_API_KEY', 'ufoTkuFj5adOiO1Mjhdb-qEL7s-HWMa3');

// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access to this file is not allowed.');
}
?>
