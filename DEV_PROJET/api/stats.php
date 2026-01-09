<?php
// api/stats.php
require_once 'db.php';
requireAuth();

$conn = getDBConnection();
if (!$conn) {
    sendJson(['error' => 'Connection failed'], 500);
}

$dbName = $_SESSION['db_config']['database'];

try {
    // 1. Basic Counts
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?");
    $stmt->bind_param("s", $dbName);
    $stmt->execute();
    $tableCount = $stmt->get_result()->fetch_assoc()['count'];

    // 2. Size Stats
    $stmt = $conn->prepare("SELECT SUM(data_length + index_length) as size, AVG(data_length + index_length) as avg_size FROM information_schema.tables WHERE table_schema = ?");
    $stmt->bind_param("s", $dbName);
    $stmt->execute();
    $sizeStats = $stmt->get_result()->fetch_assoc();

    // 3. Index Count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = ?");
    $stmt->bind_param("s", $dbName);
    $stmt->execute();
    $indexCount = $stmt->get_result()->fetch_assoc()['count'];

    // 4. Foreign Keys
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM information_schema.table_constraints WHERE table_schema = ? AND constraint_type = 'FOREIGN KEY'");
    $stmt->bind_param("s", $dbName);
    $stmt->execute();
    $fkCount = $stmt->get_result()->fetch_assoc()['count'];

    // 5. Primary Keys
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM information_schema.table_constraints WHERE table_schema = ? AND constraint_type = 'PRIMARY KEY'");
    $stmt->bind_param("s", $dbName);
    $stmt->execute();
    $pkCount = $stmt->get_result()->fetch_assoc()['count'];

    // 6. User List (Try/Catch as it might fail for non-root)
    $users = [];
    try {
        $userRes = $conn->query("SELECT User, Host FROM mysql.user");
        if ($userRes) {
            while ($u = $userRes->fetch_assoc()) {
                $users[] = $u['User'] . '@' . $u['Host'];
            }
        }
    } catch (Exception $e) {
        // Fallback or empty if permission denied
        $users[] = 'Current: ' . $conn->query("SELECT CURRENT_USER()")->fetch_row()[0];
    }

    // 5. Largest Tables
    $stmt = $conn->prepare("SELECT table_name as name, (data_length + index_length) as size FROM information_schema.tables WHERE table_schema = ? ORDER BY size DESC LIMIT 5");
    $stmt->bind_param("s", $dbName);
    $stmt->execute();
    $result = $stmt->get_result();
    $chartData = [];
    while ($row = $result->fetch_assoc()) {
        $chartData[] = $row;
    }

    sendJson([
        'tables' => $tableCount,
        'totalSize' => (float) $sizeStats['size'],
        'avgSize' => (float) $sizeStats['avg_size'],
        'indexes' => $indexCount,
        'foreignKeys' => $fkCount,
        'primaryKeys' => $pkCount,
        'users' => $users,
        'chartData' => $chartData
    ]);

} catch (Exception $e) {
    sendJson(['error' => $e->getMessage()], 500);
}
?>