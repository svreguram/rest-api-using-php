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

// Validate API key
if (!validateApiKey($db, $apiKey)) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

// Get and sanitize the table name
$table = $_GET['table'] ?? '';
$table = preg_replace('/[^a-zA-Z0-9_]/', '', $table); // Allow only alphanumeric and underscore

// Get limit and offset, sanitize them as integers
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if (empty($table)) {
    http_response_code(400);
    echo json_encode(["message" => "Table name is required"]);
    exit();
}

// Query to get the total count of matching records
$countQuery = "SELECT COUNT(*) as total FROM db_name.$table";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Query to select limited and offset transactions
$query = "SELECT * FROM db_name.$table ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();

$num = $stmt->rowCount();

if($num > 0) {
    $transactions_arr = array();
    $transactions_arr["total_count"] = $totalRecords;
    $transactions_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $transactions_arr["records"][] = $row;
    }

    http_response_code(200);
    echo json_encode($transactions_arr);
} else {
    http_response_code(404);
    echo json_encode(["message" => "No records found."]);
}
?>
