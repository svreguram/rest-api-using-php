<?php
function validateApiKey($db, $apiKey) {
    // Assume $providedApiKey is the API key provided by the user
    // $providedApiKey = $_GET['api_key'] ?? '';
    // Hash the provided API key
    $hashedProvidedApiKey = hash('sha256', $apiKey);
    $query = "SELECT id FROM api_users WHERE api_key = :api_key";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":api_key", $hashedProvidedApiKey);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
?>
