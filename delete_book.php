<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booknest_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    $sql = "DELETE FROM books WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php?msg=deleted");
        exit();
    } else {
        header("Location: admin_dashboard.php?msg=error");
        exit();
    }
} else {
    header("Location: admin_dashboard.php?msg=invalid");
    exit();
}

$conn->close();
?>
