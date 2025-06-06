<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "booknest_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT b.id, b.title, b.author, b.cover_url, b.pdf 
        FROM history h
        JOIN books b ON h.book_id = b.id
        WHERE h.user_id = ?
        ORDER BY h.viewed_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reading History</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      background-color: #f3e6d8;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #b3a78c;
      color: white;
      text-align: center;
      padding: 20px 0;
      font-size: 24px;
    }

    .book-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      padding: 30px;
    }

    .book-card {
      background-color: white;
      border-radius: 16px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 15px;
      text-align: center;
      transition: transform 0.2s ease;
    }

    .book-card:hover {
      transform: scale(1.03);
    }

    .book-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 12px;
    }

    .book-card h3 {
      color: #5c4400;
      font-size: 18px;
      margin: 10px 0 5px;
    }

    .book-card p {
      color: #5c4400;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .btn {
      background-color: #b3a78c;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
      transition: background-color 0.2s ease;
    }

    .btn:hover {
      background-color: #998e77;
    }

    .bottom-nav {
      position: fixed;
      bottom: 0;
      width: 100%;
      background-color: #b3a78c;
      display: flex;
      justify-content: space-around;
      padding: 10px 0;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    }

    .bottom-nav button {
      background: none;
      border: none;
      font-size: 18px;
      color: white;
      cursor: pointer;
    }

    .bottom-nav button:hover {
      color: #f3e6d8;
    }

    .no-history {
      text-align: center;
      font-size: 18px;
      color: #5c4400;
      margin: 50px 0;
    }
  </style>
</head>
<body>

<header>
  Your Reading History
</header>

<section class="history-section">
  <?php if ($result->num_rows > 0): ?>
    <div class="book-list">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="book-card">
          <img src="<?= htmlspecialchars($row['cover_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" />
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p>Author: <?= htmlspecialchars($row['author']) ?></p>
          <a href="reader.php?pdf=<?= urlencode($row['pdf']) ?>" class="btn">📖 Read Again</a>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="no-history">You have no reading history yet.</p>
  <?php endif; ?>
</section>

<nav class="bottom-nav">
  <button onclick="location.href='index.html'">🏠 Home</button>
  <button onclick="location.href='shelf.php'">🛒 Shelf</button>
  <button onclick="location.href='trending.php'">🔥 Trending</button>
</nav>

</body>
</html>

<?php
$conn->close();
?>
