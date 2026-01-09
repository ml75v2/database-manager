<?php
// api/setup_data.php
require_once 'db.php';
requireAuth();

$conn = getDBConnection();
if (!$conn) {
    sendJson(['error' => 'Database connection failed'], 500);
}

try {
    // 1. Users Table
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Products Table
    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        stock INT DEFAULT 0,
        category VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Orders Table
    $conn->query("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    // Check if data exists
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        // Seed Users
        $conn->query("INSERT INTO users (name, email, role) VALUES 
            ('Alice Admin', 'alice@example.com', 'admin'),
            ('Bob User', 'bob@example.com', 'user'),
            ('Charlie shopper', 'charlie@example.com', 'user')
        ");

        // Seed Products
        $conn->query("INSERT INTO products (name, price, stock, category) VALUES 
            ('Gaming Laptop', 1299.99, 10, 'Electronics'),
            ('Wireless Mouse', 49.99, 50, 'Electronics'),
            ('Coffee Mug', 12.50, 100, 'Home'),
            ('Desk Chair', 199.00, 15, 'Furniture'),
            ('Mechanical Keyboard', 89.99, 25, 'Electronics')
        ");

        // Seed Orders (Randomly assigned)
        $conn->query("INSERT INTO orders (user_id, total, status) VALUES 
            (2, 49.99, 'completed'),
            (3, 1299.99, 'pending'),
            (2, 199.00, 'completed'),
            (3, 12.50, 'cancelled')
        "); // Assuming IDs 1, 2, 3 generated in order.
    }

    sendJson(['success' => true, 'message' => 'Sample data generated successfully']);

} catch (Exception $e) {
    sendJson(['error' => $e->getMessage()], 500);
}
?>