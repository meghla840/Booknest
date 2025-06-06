<?php
header('Content-Type: application/json');

$host   = 'localhost';
$dbname = 'booknest_db';
$user   = 'root';
$pass   = '';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if ($email === '') {
    echo json_encode(['error' => 'Email is required']);
    exit;
}

// 1) Read current status
$stmt = $conn->prepare("SELECT blocked FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['error' => 'No user found with this email']);
    exit;
}
$current = (int)$res->fetch_assoc()['blocked'];
$stmt->close();

// 2) Toggle
$newStatus = $current === 1 ? 0 : 1;
$message   = $newStatus
    ? 'User blocked successfully'
    : 'User unblocked successfully';

// 3) Update (treat any non-SQL-error as success)
$upd = $conn->prepare("UPDATE users SET blocked = ? WHERE email = ?");
$upd->bind_param('is', $newStatus, $email);
if ($upd->execute()) {
    // We don’t care about affected_rows here
    echo json_encode(['success' => $message, 'newStatus' => $newStatus]);
} else {
    // Only real SQL errors land here
    error_log("SQL Error on block_user.php: " . $upd->error);
    echo json_encode(['error' => 'Database error: ' . $upd->error]);
}
$upd->close();
$conn->close();
