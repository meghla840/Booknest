<?php
session_start();
if (!isset($_GET['id'])) {
    echo "No user selected.";
    exit();
}

$conn = new mysqli("localhost", "root", "", "booknest_db");
if ($conn->connect_error) {
    die("DB connection failed.");
}

$id = intval($_GET['id']);

// Fetch user data
$stmt = $conn->prepare("SELECT name, email, profilePic, created_at, role, blocked FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

// Shelf Count
$shelfCount = 0;
$wishCount = 0;
$historyCount = 0;

$shelfRes = $conn->query("SELECT COUNT(*) as c FROM shelf WHERE user_id = $id");
if ($shelfRes) $shelfCount = $shelfRes->fetch_assoc()['c'];

$wishRes = $conn->query("SELECT COUNT(*) as c FROM wishlist WHERE user_id = $id");
if ($wishRes) $wishCount = $wishRes->fetch_assoc()['c'];

$historyRes = $conn->query("SELECT COUNT(*) as c FROM history WHERE user_id = $id");
if ($historyRes) $historyCount = $historyRes->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($user['name']) ?>'s Profile</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f3e6d8;
      animation: fadeIn 1.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .profile-container {
      background-color: #fff;
      max-width: 550px;
      margin: 60px auto;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.12);
      text-align: center;
      transition: all 0.4s ease-in-out;
    }

    .profile-container:hover {
      transform: scale(1.02);
    }

    .profile-container h2 {
      font-size: 28px;
      color: #333;
      margin-bottom: 10px;
    }

    .profile-container img {
      width: 150px;
      height: 150px;
      border-radius: 100px;
      object-fit: cover;
      margin: 20px 0;
      border: 3px solid #b3a78c;
    }

    .profile-container p {
      font-size: 16px;
      color: #444;
      margin-bottom: 10px;
    }

    .qr-code {
      margin-top: 20px;
    }

    .back-btn {
      display: inline-block;
      margin-top: 25px;
      padding: 10px 22px;
      background-color: #b3a78c;
      color: white;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: #998e77;
    }

    .info-group {
      text-align: left;
      margin-top: 20px;
      line-height: 1.8;
    }

    .info-group span.label {
      font-weight: bold;
      color: #5c4400;
    }
  </style>
</head>
<body>

  <div class="profile-container">
    <h2><?= htmlspecialchars($user['name']) ?>'s Profile</h2>
    <img src="<?= htmlspecialchars($user['profilePic']) ?>" alt="Profile Picture">
    
    <div class="info-group">
      <p><span class="label">Email:</span> <?= htmlspecialchars($user['email']) ?></p>
      <p><span class="label">Role:</span> <?= htmlspecialchars($user['role'] ?? 'User') ?></p>
      <p><span class="label">Status:</span>
        <?php if ($user['blocked']) { ?>
          <span style="color: red; font-weight: bold;">Blocked</span>
        <?php } else { ?>
          <span style="color: green; font-weight: bold;">Active</span>
        <?php } ?>
      </p>
      <p><span class="label">Joined on:</span> <?= date("F j, Y", strtotime($user['created_at'])) ?></p>
      <p><span class="label">Books on Shelf:</span> <?= $shelfCount ?></p>
      <p><span class="label">Wishlist:</span> <?= $wishCount ?></p>
      <p><span class="label">History:</span> <?= $historyCount ?></p>
    </div>


    <a class="back-btn" href="admin_dashboard.php">← Back to Dashboard</a>
  </div>

</body>
</html>
