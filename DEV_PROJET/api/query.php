<?php
// api/query.php
require_once 'db.php';
requireAuth();

$input = json_decode(file_get_contents('php://input'), true);
$sql = $input['sql'] ?? '';

if (!$sql)
    sendJson(['error' => 'SQL is required'], 400);

$conn = getDBConnection();
if (!$conn)
    sendJson(['error' => 'Connection failed'], 500);

try {
    // We use the direct query method to support various SQL types
    // Note: multi_query might be needed for multiple statements, but let's stick to query for safety/simplicity first.
    $result = $conn->query($sql);

    if ($result === true) {
        // Successful DML/DDL
        sendJson([
            'success' => true,
            'message' => "Query executed successfully. Affected rows: " . $conn->affected_rows,
            'affectedRows' => $conn->affected_rows,
            'data' => []
        ]);
    } elseif ($result instanceof mysqli_result) {
        // Successful SELECT (DQL)
        $data = [];
        $fields = [];

        while ($f = $result->fetch_field()) {
            $fields[] = $f->name;
        }

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        sendJson([
            'success' => true,
            'message' => "Fetched " . count($data) . " rows",
            'data' => $data,
            'fields' => $fields
        ]);
    } else {
        // Error
        throw new Exception($conn->error);
    }

} catch (Exception $e) {
    sendJson(['success' => false, 'error' => $e->getMessage()], 400);
}
?>