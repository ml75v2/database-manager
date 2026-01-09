<?php
// api/tables.php
require_once 'db.php';
requireAuth();

$conn = getDBConnection();
if (!$conn)
    sendJson(['error' => 'Connection failed'], 500);

$dbName = $_SESSION['db_config']['database'];
$tableName = $_GET['name'] ?? null;
$action = $_GET['action'] ?? 'list'; // list, structure, content

try {
    if (!$tableName) {
        // LIST TABLES
        $stmt = $conn->prepare("SELECT table_name as name, table_rows as rows_count, (data_length + index_length) as size, table_collation as collation FROM information_schema.tables WHERE table_schema = ?");
        $stmt->bind_param("s", $dbName);
        $stmt->execute();
        $result = $stmt->get_result();
        $tables = [];
        while ($row = $result->fetch_assoc()) {
            $tables[] = $row;
        }
        sendJson($tables);
    } else {
        // Specific Table Actions
        /* 
           SECURITY WARNING: Table names in MySQL cannot be easily bound depending on context.
           We must escape them manually. 
        */
        $escapedTable = "`" . $conn->real_escape_string($tableName) . "`";

        if ($action === 'structure') {
            $result = $conn->query("SHOW FULL COLUMNS FROM $escapedTable");
            $columns = [];
            while ($row = $result->fetch_assoc())
                $columns[] = $row;

            $result = $conn->query("SHOW INDEX FROM $escapedTable");
            $indexes = [];
            while ($row = $result->fetch_assoc())
                $indexes[] = $row;

            sendJson(['columns' => $columns, 'indexes' => $indexes]);

        } elseif ($action === 'content') {
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
            $offset = ($page - 1) * $limit;
            $search = $_GET['search'] ?? '';
            $sort = $_GET['sort'] ?? '';
            $order = strtoupper($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

            // Get columns first for validation and search construction
            $colRes = $conn->query("SHOW COLUMNS FROM $escapedTable");
            $columns = [];
            while ($c = $colRes->fetch_assoc()) {
                $columns[] = $c['Field'];
            }

            // Build Query Parts
            $whereSQL = "";
            $params = [];
            $types = "";

            if ($search !== '') {
                $whereSQL = "WHERE ";
                $conds = [];
                $term = "%$search%";
                foreach ($columns as $col) {
                    $conds[] = "`$col` LIKE ?";
                    $params[] = $term;
                    $types .= "s";
                }
                $whereSQL .= implode(" OR ", $conds);
            }

            $orderSQL = "";
            if ($sort && in_array($sort, $columns)) {
                $orderSQL = "ORDER BY `$sort` $order";
            }

            // Count total
            $countQuery = "SELECT COUNT(*) as total FROM $escapedTable $whereSQL";
            $stmt = $conn->prepare($countQuery);
            if ($search !== '') {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'];

            // Fetch data
            $dataQuery = "SELECT * FROM $escapedTable $whereSQL $orderSQL LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($dataQuery);
            if ($search !== '') {
                // Add limit/offset to params
                $params[] = $limit;
                $params[] = $offset;
                $types .= "ii";
                $stmt->bind_param($types, ...$params);
            } else {
                $stmt->bind_param("ii", $limit, $offset);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            sendJson([
                'rows' => $rows,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'totalPages' => ceil($total / $limit)
                ]
            ]);
        }
    }
} catch (Exception $e) {
    sendJson(['error' => $e->getMessage()], 500);
}
?>