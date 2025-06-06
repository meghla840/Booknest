<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booknest_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$sql = "SELECT book_id, name, author, description, image, pdf FROM books ORDER BY book_id DESC";
$result = $conn->query($sql);

$books = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $books[] = [
            "book_id" => $row['book_id'],
            "name" => $row['name'],
            "author" => $row['author'],
            "description" => $row['description'],
            "image" => "uploads/images/" . $row['image'],
            "pdf" => "uploads/pdfs/" . $row['pdf']
        ];
    }
}

echo json_encode($books);

$conn->close();
?>
