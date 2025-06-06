<?php
session_start();
include 'db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (!$name || !$email || !$password || !$confirm_password) {
        $message = 'Please fill all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
    } else {
        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = 'Email already registered.';
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $blocked = 0;

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, blocked) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $blocked);

            if ($stmt->execute()) {
                $message = 'Registration successful! <a href="login.php">Login here</a>.';
            } else {
                $message = 'Registration failed. Try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up - Booknest</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f3e6d8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .signup-container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    input[type=text], input[type=email], input[type=password] {
      width: 100%;
      padding: 10px;
      margin: 10px 0 20px 0;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
    }
    button {
      background: #d5bf8f;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      width: 100%;
      transition: 0.3s;
    }
    button:hover {
      background: #241a02;
    }
    .message {
      margin-bottom: 15px;
      color: red;
      font-weight: 600;
    }
    a {
      color: #241a02;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <div class="signup-container">
    <h2>Create your Booknest account</h2>
    <?php if ($message) { echo "<div class='message'>$message</div>"; } ?>
    <form method="post" action="signup.php">
      <input type="text" name="name" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required />
      <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>

</body>
</html>
