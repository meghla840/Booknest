<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "booknest_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'] ?? null;

if (!$book_id) {
    echo json_encode(["status" => "error", "message" => "Book ID missing"]);
    exit;
}

// Insert history record with current timestamp
$stmt = $conn->prepare("INSERT INTO history (user_id, book_id, viewed_at) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $user_id, $book_id);
if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to insert history"]);
    exit;
}

// Count reads of this book by this user in last 1 minute
$count_stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM history WHERE user_id = ? AND book_id = ? AND viewed_at >= (NOW() - INTERVAL 1 MINUTE)");
$count_stmt->bind_param("ii", $user_id, $book_id);
$count_stmt->execute();
$count_res = $count_stmt->get_result();
$count_row = $count_res->fetch_assoc();

$read_count = (int)$count_row['cnt'];

// If read 3 or more times in last 1 minute, add to trending if not already added in last 3 minutes
if ($read_count >= 3) {
    $check_trending = $conn->prepare("SELECT id FROM trending WHERE book_id = ? AND added_time >= (NOW() - INTERVAL 3 MINUTE)");
    $check_trending->bind_param("i", $book_id);
    $check_trending->execute();
    $check_res = $check_trending->get_result();

    if ($check_res->num_rows === 0) {
        $insert_trending = $conn->prepare("INSERT INTO trending (book_id) VALUES (?)");
        $insert_trending->bind_param("i", $book_id);
        $insert_trending->execute();
    }
}

echo json_encode(["status" => "success"]);
$conn->close();
