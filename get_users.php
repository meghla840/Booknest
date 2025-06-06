<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booknest_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Use correct column name 'blocked' instead of 'is_blocked'
$sql = "SELECT id, name, email, profilePic, blocked FROM users";
$result = $conn->query($sql);

$users = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "email" => $row['email'],
            "profilePic" => $row['profilePic'] ?: null,
            "is_blocked" => (bool)$row['blocked']  // still using is_blocked in frontend for consistency
        ];
    }
}

echo json_encode($users);
$conn->close();
?>
