<?php
// api/login.php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    sendJson(['error' => 'Invalid JSON'], 400);
}

$host = $input['host'] ?? 'localhost';
$user = $input['user'] ?? 'root';
$password = $input['password'] ?? '';
$database = $input['database'] ?? '';

if (!$database) {
    sendJson(['error' => 'Database name is required'], 400);
}

// Test connection
try {
    // Suppress warnings
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // If create_db flag is set, connect without DB first
    if (!empty($input['create'])) {
        $conn = new mysqli($host, $user, $password);
        if ($conn->connect_error) {
            throw new Exception("Connection failed (No DB): " . $conn->connect_error);
        }

        // Sanitize db name (basic)
        $dbName = preg_replace('/[^a-zA-Z0-9_]/', '', $database);
        if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName`")) {
            throw new Exception("Error creating database: " . $conn->error);
        }
        $conn->close();
    }

    // Now connect with the database
    $conn = new mysqli($host, $user, $password, $database);

    // If successful, store in session
    $_SESSION['db_config'] = [
        'host' => $host,
        'user' => $user,
        'password' => $password,
        'database' => $database
    ];

    $conn->close();
    sendJson(['success' => true, 'message' => 'Connected successfully']);

} catch (Throwable $e) {
    // Catch both Exception and Error
    sendJson(['success' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 400);
}
?>