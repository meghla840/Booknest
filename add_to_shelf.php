<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json'); // JSON response

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// book_id POST method diye ashbe
if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // check if already in shelf
    $check = $conn->prepare("SELECT * FROM shelf WHERE user_id = ? AND book_id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows == 0) {
        // not found, so insert
        $stmt = $conn->prepare("INSERT INTO shelf (user_id, book_id, added_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'exists']);
    }

    $check->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No book selected']);
}
?>
