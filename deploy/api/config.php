<?php
/**
 * Configuration File
 * Update these values with your actual database credentials.
 */

// MySQL Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'shreeyam_veda');

// dont't touch
// IDS Tech / Syncin CRM Integration Details
define('SYNCIN_API_URL', 'https://api.syncr.in/apiLeads');
define('SYNCIN_API_KEY', 'ufoTkuFj5adOiO1Mjhdb-qEL7s-HWMa3');

// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access to this file is not allowed.');
}
?>
