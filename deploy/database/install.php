<?php
/**
 * Shreeyam Veda — One-Time Database Installer (MySQLi)
 * =====================================================
 * Upload this file to your server root (e.g. public_html/install.php)
 * and open it in a browser ONCE to create the database and table.
 *
 * ⚠️  DELETE THIS FILE IMMEDIATELY AFTER RUNNING IT.
 */

// ── Update these values before uploading ──────────────────────
$host = 'localhost';   // usually 'localhost' on cPanel/shared hosting
$user = 'root';        // your DB username (e.g. cpanel_username_dbuser)
$pass = '';            // your DB password
// ──────────────────────────────────────────────────────────────

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("<h2 style='color:red;font-family:Arial'>❌ Connection failed: " . htmlspecialchars($conn->connect_error) . "</h2>");
}

$queries = [
    "CREATE DATABASE IF NOT EXISTS `shreeyam_veda` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "USE `shreeyam_veda`",
    "CREATE TABLE IF NOT EXISTS `submissions` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name`       VARCHAR(255) NOT NULL                COMMENT 'Full name of the lead',
        `phone`      VARCHAR(20)  NOT NULL                COMMENT 'Primary contact number',
        `email`      VARCHAR(255) NOT NULL DEFAULT ''     COMMENT 'Email address (optional)',
        `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
        PRIMARY KEY (`id`),
        INDEX `idx_phone`      (`phone`),
        INDEX `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
      COMMENT='Lead submissions from Shreeyam Veda website'"
];

echo "<h2 style='font-family:Arial'>Shreeyam Veda — Database Installer (MySQLi)</h2><ul style='font-family:monospace'>";

$allOk = true;
foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "<li style='color:green'>✔ " . htmlspecialchars(substr($q, 0, 80)) . (strlen($q) > 80 ? '…' : '') . "</li>";
    } else {
        echo "<li style='color:red'>✘ Error: " . htmlspecialchars($conn->error) . "</li>";
        $allOk = false;
    }
}

echo "</ul>";
$conn->close();

if ($allOk) {
    echo "<p style='font-family:Arial;color:green'><strong>✅ Database and table created successfully!</strong></p>";
    echo "<p style='font-family:Arial;color:red'><strong>⚠️ Please DELETE this file (install.php) from your server immediately!</strong></p>";
} else {
    echo "<p style='font-family:Arial;color:red'><strong>❌ Some steps failed. Check the errors above and try again.</strong></p>";
}
?>
