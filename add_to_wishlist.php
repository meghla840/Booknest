<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "booknest_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection failed: " . $conn->connect_error]);
    exit;
}

// Debug: Check session
// var_dump($_SESSION); exit;

$user_id = $_SESSION['user_id'] ?? null;  // User login hole session e thakbe
$book_id = $_POST['book_id'] ?? null;

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

if (!$book_id) {
    echo json_encode(["status" => "error", "message" => "No book ID received"]);
    exit;
}

// Check if book already in wishlist
$check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?");
$check->bind_param("ii", $user_id, $book_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "exists"]);
    exit;
}

// Insert into wishlist
$stmt = $conn->prepare("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $book_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$conn->close();
?>
