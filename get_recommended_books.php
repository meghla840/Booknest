<?php
session_start();

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'booknest_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$books = [];

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Get genres from search history — corrected column name here
    $stmt = $conn->prepare("SELECT DISTINCT genre FROM user_search_history WHERE user_id = ? ORDER BY search_time DESC LIMIT 5");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $genres = [];
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row['genre'];
    }

    $stmt->close();

    if (!empty($genres)) {
        // Build SQL to fetch books based on genres
        $placeholders = implode(',', array_fill(0, count($genres), '?'));
        $types = str_repeat('s', count($genres));
        $stmt = $conn->prepare("SELECT id, title, author, genre, cover_url FROM books WHERE genre IN ($placeholders) ORDER BY RAND() LIMIT 6");
        $stmt->bind_param($types, ...$genres);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

// If no genres or not logged in, get random books
if (!isset($result) || $result->num_rows === 0) {
    $result = $conn->query("SELECT id, title, author, genre, cover_url FROM books ORDER BY RAND() LIMIT 10");
}

while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

header('Content-Type: application/json');
echo json_encode($books);
$conn->close();
?>
