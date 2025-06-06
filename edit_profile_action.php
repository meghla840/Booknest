<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id'];
$name = $_POST['name'] ?? '';

$profilePicPath = $user['profilePic']; // default to current one

// Check if new profile picture is uploaded
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profilePic']['tmp_name'];
    $fileName = $_FILES['profilePic']['name'];
    $fileSize = $_FILES['profilePic']['size'];
    $fileType = $_FILES['profilePic']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadFileDir = 'images/';
        $destPath = $uploadFileDir . $newFileName;

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $profilePicPath = $destPath;
        }
    }
}

// Database update
$conn = new mysqli("localhost", "root", "", "booknest_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("UPDATE users SET name = ?, profilePic = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $profilePicPath, $userId);
$stmt->execute();
$stmt->close();

// Update session info
$_SESSION['user']['name'] = $name;
$_SESSION['user']['profilePic'] = $profilePicPath;

$conn->close();

header("Location: profile.php");
exit();
?>
