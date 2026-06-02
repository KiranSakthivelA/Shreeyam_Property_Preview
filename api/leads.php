<?php
require_once 'config.php';

// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

// Get JSON POST body
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->name) || empty($data->name) || !isset($data->phone) || empty($data->phone)) {
    http_response_code(400);
    echo json_encode(["error" => "Name and phone are required."]);
    exit();
}

$name = htmlspecialchars(strip_tags($data->name));
$phone = htmlspecialchars(strip_tags($data->phone));
$email = isset($data->email) ? htmlspecialchars(strip_tags($data->email)) : '';

$insertId = null;

// 1. Save to MySQL Database
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Check if table exists, if not you should run the SQL setup
    $sql = "INSERT INTO submissions (name, phone, email) VALUES (:name, :phone, :email)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $name, 'phone' => $phone, 'email' => $email]);
    
    $insertId = $pdo->lastInsertId();
} catch (PDOException $e) {
    // We log the error but still try to push to CRM if possible
    error_log("Database Error: " . $e->getMessage());
    // If you strictly require DB insert to succeed, uncomment below:
    /*
    http_response_code(500);
    echo json_encode(["error" => "Failed to save submission. Check database connection."]);
    exit();
    */
}

// 2. Push to Syncr CRM using cURL
if (defined('SYNCR_API_URL') && SYNCR_API_URL !== 'https://api.syncr.com/webhook/placeholder') {
    $payload = json_encode([
        "name" => $name,
        "phone" => $phone,
        "email" => $email
    ]);

    $ch = curl_init(SYNCR_API_URL);
    
    $headers = [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ];
    
    if (defined('SYNCR_API_KEY') && SYNCR_API_KEY !== 'your_api_key_here') {
        $headers[] = 'Authorization: Bearer ' . SYNCR_API_KEY;
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        error_log('cURL Error pushing to Syncr CRM: ' . curl_error($ch));
    } else if ($httpCode < 200 || $httpCode >= 300) {
        error_log("Syncr CRM responded with status: $httpCode");
    }
    
    curl_close($ch);
}

// Send success response
http_response_code(201);
echo json_encode(["message" => "Submission saved successfully!", "id" => $insertId]);
?>
