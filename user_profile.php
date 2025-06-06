<?php
include 'backend/config.php';

$user_id = $_GET['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
  echo "User not found!";
  exit;
}

$isBlocked = ($user['status'] ?? '') === 'Blocked';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($user['name']) ?> - Profile | Booknest</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    /* Same styles as before */
    body {
      font-family: 'Poppins', sans-serif;
      background: #f3e6d8;
      margin: 0;
      padding: 40px;
    }

    .profile-card {
      background: #fff;
      max-width: 500px;
      margin: auto;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      padding: 30px;
      text-align: center;
    }

    .profile-card img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 20px;
      border: 4px solid #d5bf8f;
    }

    .profile-card h2 {
      margin: 0;
      font-size: 26px;
      color: #382c13;
    }

    .profile-card p {
      font-size: 15px;
      color: #555;
      margin: 5px 0;
    }

    .btn-back, .btn-block {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 600;
      font-size: 15px;
      border: none;
      color: white;
      transition: background 0.3s;
    }

    .btn-back {
      background: #d5bf8f;
    }
    .btn-back:hover {
      background: #241a02;
    }

    .btn-block {
      margin-left: 15px;
      background: <?= $isBlocked ? '#4caf50' : '#e53935' ?>;
    }
    .btn-block:hover {
      background: <?= $isBlocked ? '#388e3c' : '#b71c1c' ?>;
    }
  </style>
</head>
<body>

  <div class="profile-card">
    <img src="<?= htmlspecialchars($user['profilePic'] ?: 'https://example.com/default.jpg') ?>" alt="Profile" />
    <h2><?= htmlspecialchars($user['name']) ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($user['status'] ?? 'Active') ?></p>
    <p><strong>Joined:</strong> <?= date('d M Y', strtotime($user['created_at'] ?? '')) ?></p>

    <a class="btn-back" href="javascript:history.back()">← Back</a>

    <form method="POST" action="backend/block_user.php" style="display:inline-block;">
      <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
      <button type="submit" class="btn-block">
        <?= $isBlocked ? 'Unblock User' : 'Block User' ?>
      </button>
    </form>
  </div>

</body>
</html>
