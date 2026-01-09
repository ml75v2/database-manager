<?php
// api/db.php
session_start();

header('Content-Type: application/json');

function getDBConnection() {
    if (!isset($_SESSION['db_config'])) {
        return null;
    }

    $config = $_SESSION['db_config'];
    
    try {
        /*
          Using mysqli as it's common and robust.
          Could use PDO if preferred, but mysqli is fine for this scope.
        */
        // Suppress warnings to handle errors manually
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        $conn = new mysqli(
            $config['host'], 
            $config['user'], 
            $config['password'], 
            $config['database']
        );
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        return null; // Let caller handle
    }
}

function sendJson($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function requireAuth() {
    if (!isset($_SESSION['db_config'])) {
        sendJson(['error' => 'Not authenticated'], 401);
    }
}
?>
