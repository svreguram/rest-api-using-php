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

$query = "DESCRIBE $tableName";
$stmt = $db->prepare($query);

try {
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $dataTypes = [];
    foreach ($columns as $column) {
        $dataTypes[$column['Field']] = $column['Type'];
    }

    http_response_code(200);
    echo json_encode($dataTypes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error executing query: " . $e->getMessage()]);
}
?>
