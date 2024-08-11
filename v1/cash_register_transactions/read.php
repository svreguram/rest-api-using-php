<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../v1/config/dbcon.php';
include_once '../../v1/validations/validate_api_key.php';

$database = new Database();
$db = $database->getConnection();

// $apiKey = $_GET['api_key'] ?? '';
// $date = $_GET['date'] ?? ''; // Get the date from the query parameter
$startDate = $_GET['start_date'] ?? ''; // Get the start date from the query parameter
// Get API key from the custom header x-api-key
$headers = apache_request_headers();
$apiKey = isset($headers['x-api-key']) ? trim($headers['x-api-key']) : '';
$endDate = $_GET['end_date'] ?? ''; // Get the end date from the query parameter
$limit = $_GET['limit'] ?? 100; // Get the limit from the query parameter, default is 100
$offset = $_GET['offset'] ?? 0; // Get the offset from the query parameter, default is 0 (first record)

if (!validateApiKey($db, $apiKey)) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

// Query to select all products
// $query = "SELECT * FROM products WHERE customer_id = (SELECT id FROM users WHERE api_key = :api_key)";
// Query to get the total count of matching records
// Query to get the total count of matching records
$countQuery = "SELECT COUNT(*) as total FROM db_name.cash_register_transactions WHERE DATE(created_at) BETWEEN :start_date AND :end_date";
$countStmt = $db->prepare($countQuery);
$countStmt->bindParam(":start_date", $startDate);
$countStmt->bindParam(":end_date", $endDate);
$countStmt->execute();
$totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Query to select limited and offset transactions within the date range
$query = "SELECT * FROM db_name.cash_register_transactions WHERE DATE(created_at) BETWEEN :start_date AND :end_date ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
$stmt->bindParam(":start_date", $startDate);
$stmt->bindParam(":end_date", $endDate);
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
    echo json_encode(["message" => "No transactions found."]);
}
?>