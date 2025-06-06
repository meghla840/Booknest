<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

$profilePic = !empty($user['profilePic']) ? $user['profilePic'] : 'images/default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Profile - Booknest</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f3e6d8;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .profile-container {
      background-color: #e5dac2;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      width: 100%;
      max-width: 1000px;
      margin: 50px auto;
      text-align: center;
    }
    .profile-container img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      margin-bottom: 10px;
      object-fit: cover;
    }
    .profile-container h2 {
      margin-bottom: 10px;
    }
    .profile-container p {
      margin: 5px 0;
    }
    .btn {
      background: #d5bf8f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
      margin-top: 10px;
    }
    .btn:hover {
      background: #241a02;
    }
    .button-row {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
      flex-wrap: wrap;
    }
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background: #fff3e0;
      display: flex;
      justify-content: space-around;
      border-top: 1px solid #ccc;
      padding: 10px 0;
    }
    .bottom-nav button {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      font-weight: bold;
      color: #5c4400;
    }
    .bottom-nav button:hover {
      color: #241a02;
    }
    /* Edit Profile Form Style */
    #edit-profile-form {
      display: none;
      margin-top: 20px;
      text-align: left;
    }
    #edit-profile-form input,
    #edit-profile-form button {
      margin-top: 10px;
      padding: 10px;
      width: 100%;
      max-width: 400px;
    }
    /* Styling for Edit Profile Button */
    .edit-btn {
      background: transparent;
      color: #d5bf8f;
      border: 2px solid #d5bf8f;
      padding: 12px 15px;
      font-size: 20px;
      cursor: pointer;
      transition: background 0.3s;
      clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
    }
    .edit-btn:hover {
      background: #d5bf8f;
      color: white;
    }
  </style>
</head>
<body>

<div class="profile-container">
  <h2><?= $user['role'] === 'admin' ? 'Admin Profile' : 'User Profile' ?></h2>
  <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" />
  <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
  <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

  <div class="button-row">
    <?php if ($user['role'] === 'admin'): ?>
      <button class="btn" onclick="location.href='admin_dashboard.php'">🛠️ Admin Panel</button>
      <button class="btn" onclick="location.href='admin_dashboard.php#user-interface'">👥 User Interface</button>
    <?php else: ?>
      <button class="btn" onclick="location.href='wishlist.php'">📚 Wishlist</button>
      <button class="btn" onclick="location.href='history.php'">🕘 History</button>
      <button class="edit-btn" onclick="toggleEditProfile()">+</button>
    <?php endif; ?>
  </div>

  <button class="btn" onclick="logout()">🚪 Logout</button>

  <!-- Edit Profile Form -->
  <div id="edit-profile-form">
    <h3>Edit Profile</h3>
    <form action="edit_profile_action.php" method="post" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($user['name']) ?>" required />
      <input type="file" name="profilePic" accept="image/*" />
      <button type="submit">Save Changes</button>
    </form>
  </div>
</div>

<nav class="bottom-nav">
  <button onclick="location.href='index.html'">🏠 Home</button>
  <button onclick="location.href='shelf.php'">🛒 Shelf</button>
  <button onclick="location.href='trending.php'">🔥 Trending</button>
</nav>

<script>
function toggleEditProfile() {
  const form = document.getElementById('edit-profile-form');
  form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
}

function logout() {
  if(confirm('Are you sure you want to logout?')) {
    window.location.href = 'logout.php';
  }
}
</script>

</body>
</html>
