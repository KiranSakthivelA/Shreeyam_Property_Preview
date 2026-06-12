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

// Validate mandatory fields
if (!isset($data->name) || empty(trim($data->name)) || !isset($data->phone) || empty(trim($data->phone))) {
    http_response_code(400);
    echo json_encode(["error" => "Name and phone are required."]);
    exit();
}

$name  = htmlspecialchars(strip_tags(trim($data->name)));
$phone = htmlspecialchars(strip_tags(trim($data->phone)));
$email = isset($data->email) ? htmlspecialchars(strip_tags(trim($data->email))) : '';

$insertId = null;

// 1. Save to MySQL Database (optional - silently fails if not configured)
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    $sql  = "INSERT INTO submissions (name, phone, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $phone, $email);
    $stmt->execute();

    $insertId = $conn->insert_id;

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    // DB failure is non-fatal — continue to CRM push
}

// 2. Push to IDS Tech / Syncin CRM (silently fails if unreachable)
try {
    if (!function_exists('curl_init')) {
        throw new Exception("cURL is not available on this server.");
    }

    // Mandatory fields: clientFirstName, phoneNumber, leadSource
    // All optional fields use "" (empty string) per API spec
    $payloadData = [
        "projectName"          => "Veda",
        "clientNamePrefix"     => "Mr",
        "clientFirstName"      => $name,      // MANDATORY
        "clientLastName"       => "",
        "phoneNumber"          => $phone,     // MANDATORY
        "alternatePhoneNumber" => "",
        "leadSource"           => "Website",  // MANDATORY
        "campaignSource"       => "",
        "clientStreet"         => "",
        "clientCountry"        => "",
        "clientCity"           => "",
        "clientZipCode"        => "",
        "clientState"          => "",
        "leadStage"            => "",
        "propertyType"         => "",
        "location"             => "",
        "area"                 => "",
        "clientPreference"     => "",
        "clientBudgetValue"    => 0,
        "clientBudgetType"     => "",
        "description"          => "",
        "intrestedProjects"    => []
    ];

    if (!empty($email)) {
        $payloadData["email"] = $email;
    }

    $payload = json_encode($payloadData);

    $ch = curl_init(SYNCIN_API_URL);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-APP-KEY: ' . SYNCIN_API_KEY,
        'Content-Length: ' . strlen($payload)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT,        15);
    // SSL options for compatibility with shared/free hosting
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $result   = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    // curl_close($ch); // Deprecated since PHP 8.0, effect removed in 8.5

    if ($curlErr) {
        error_log("cURL Error pushing to Syncin CRM: " . $curlErr);
    } elseif ($httpCode < 200 || $httpCode >= 300) {
        error_log("Syncin CRM responded with HTTP $httpCode. Response: $result");
    } else {
        error_log("Syncin CRM push success. HTTP $httpCode. Response: $result");
    }

} catch (Exception $e) {
    error_log("CRM Push Exception: " . $e->getMessage());
    // CRM failure is non-fatal — user still gets success response
}

// Always return success to the user
http_response_code(201);
echo json_encode(["message" => "Submission saved successfully!", "id" => $insertId]);
?>
