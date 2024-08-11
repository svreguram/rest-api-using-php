<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../v1/config/dbcon.php';
include_once '../../v1/validations/validate_api_key.php';

$database = new Database();
$db = $database->getConnection();

// Get API key from the custom header x-api-key
$headers = apache_request_headers();
$apiKey = isset($headers['x-api-key']) ? trim($headers['x-api-key']) : '';

$tableName = $_GET['table_name'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

if (!validateApiKey($db, $apiKey)) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

if (empty($tableName)) {
    http_response_code(400);
    echo json_encode(["message" => "Table name is required"]);
    exit();
}

if (empty($startDate) || empty($endDate)) {
    http_response_code(400);
    echo json_encode(["message" => "Start date and end date are required"]);
    exit();
}

$query = "SELECT COUNT(*) as total_count FROM db_name.$tableName WHERE DATE(created_at) BETWEEN :start_date AND :end_date";
$stmt = $db->prepare($query);

$stmt->bindParam(':start_date', $startDate);
$stmt->bindParam(':end_date', $endDate);

try {
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode(["total_count" => $row['total_count']]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error executing query: " . $e->getMessage()]);
}
?>
