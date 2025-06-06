<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "booknest_db");

// Connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only run when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collecting input fields
    $id = $_POST['id'];
    $book_name = $_POST['book_name'];
    $author_name = $_POST['author_name'];
    $genre = $_POST['genre'];

    // Handle file uploads
    $coverPath = '';
    $pdfPath = '';

    // Book image
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] === 0) {
        $coverPath = 'uploads/' . basename($_FILES['book_image']['name']);
        move_uploaded_file($_FILES['book_image']['tmp_name'], $coverPath);
    }

    // Book PDF
    if (isset($_FILES['book_pdf']) && $_FILES['book_pdf']['error'] === 0) {
        $pdfPath = 'uploads/' . basename($_FILES['book_pdf']['name']);
        move_uploaded_file($_FILES['book_pdf']['tmp_name'], $pdfPath);
    }

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO books (id, title, author, genre, cover_url, pdf) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id, $book_name, $author_name, $genre, $coverPath, $pdfPath);

    // Execute and check
    if ($stmt->execute()) {

        header("Location: admin_dashboard.php");
        exit();

    } else {
        echo "❌ Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close DB connection
$conn->close();
?>
