<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];

    // Fetch current status
    $sql = "SELECT status FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $_SESSION['message'] = "User not found!";
        header("Location: ../admin_dashboard.php");
        exit;
    }

    $currentStatus = $user['status'] ?? 'Active';
    $newStatus = ($currentStatus === 'Blocked') ? 'Active' : 'Blocked';

    // Update user status
    $update_sql = "UPDATE users SET status = '$newStatus' WHERE id = '$user_id'";
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['message'] = "User status updated to $newStatus.";
    } else {
        $_SESSION['message'] = "Error updating status.";
    }

    // Redirect back to profile page or admin dashboard
    header("Location: ../user_profile.php?user_id=$user_id");
    exit;
}
?>
