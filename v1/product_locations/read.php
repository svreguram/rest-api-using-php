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

$countQuery = "SELECT COUNT(*) as total FROM db_name.product_locations";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];


$query = "SELECT * FROM db_name.product_locations";
$stmt = $db->prepare($query);
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