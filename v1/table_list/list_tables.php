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

if (!validateApiKey($db, $apiKey)) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

$query = "SHOW TABLES";
$stmt = $db->prepare($query);
$stmt->execute();

$tables = [];

while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

if(count($tables) > 0) {
    http_response_code(200);
    echo json_encode(["tables" => $tables]);
} else {
    http_response_code(404);
    echo json_encode(["message" => "No tables found."]);
}
?>
