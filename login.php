<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "booknest_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($email === "admin@booknest.com" && $password === "admin123") {
        $_SESSION['user'] = [
            "email" => "admin@booknest.com",
            "name" => "Admin",
            "role" => "admin",
            "blocked" => false,
            "profilePic" => ""
        ];
        header("Location: admin_dashboard.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($user['blocked']) {
                $message = "Your account is blocked.";
            } else {
                $_SESSION['user'] = $user;
              
        $_SESSION['user_id'] = $user['id'];
                header("Location: profile.php");
                exit();
            }
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Email not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!-- login form html (unchanged design) নিচে থাকবে -->


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - Booknest</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f3e6d8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-container {
      background: #ffffff;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    .login-container h2 {
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
    }
    .btn {
      background: #d5bf8f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      width: 100%;
    }
    .btn:hover {
      background: #241a02;
    }
    .login-option {
      margin-top: 20px;
      font-size: 14px;
    }
    .login-option a {
      color: #241a02;
      text-decoration: none;
      font-weight: 600;
    }
    .error-msg {
      color: red;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login to Booknest</h2>
    <?php if ($message): ?>
      <div class="error-msg"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <input type="email" name="email" placeholder="Enter your email" required />
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Enter your password" required />
      </div>
      <button class="btn" type="submit">Login</button>
    </form>

    <div class="login-option">
      <p>Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
  </div>
</body>
</html>
