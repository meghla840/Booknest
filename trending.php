<?php
session_start();
$conn = new mysqli("localhost", "root", "", "booknest_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
   
}
 $conn->query("DELETE FROM trending WHERE added_time <= (NOW() - INTERVAL 3 MINUTE)");

$user_id = $_SESSION['user_id'] ?? 0;

$sql = "SELECT b.id, b.title, b.author, b.cover_url, b.pdf, MAX(t.added_time) as last_trending_time 
        FROM trending t
        JOIN books b ON t.book_id = b.id
        WHERE t.added_time > (NOW() - INTERVAL 3 MINUTE)
        GROUP BY b.id
        ORDER BY last_trending_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Trending Books</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
 body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f3e6d8;
  margin: 0;
  padding: 0;
  color: #5c4400;
}

header {
  background-color: #b3a78c;
  padding: 18px 20px;
  text-align: center;
  color: white;
  font-size: 1.6rem;
  font-weight: bold;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  position: sticky;
  top: 0;
  z-index: 100;
}

.section {
  padding: 30px 20px;
  max-width: 1200px;
  margin: auto;
}

.book-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 24px;
  justify-items: center;
  align-items: start;
}


.book-card {
  background: #fff8f0;
  border-radius: 16px;
  box-shadow: 0 6px 16px rgba(179, 167, 140, 0.3);
  padding: 18px 15px 20px;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.35s ease, box-shadow 0.35s ease;
  height: 360px;
  width: 100%;
  max-width: 250px;
  box-sizing: border-box;
  cursor: default;
}

.book-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 30px rgba(179, 167, 140, 0.5);
}

.book-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 12px;
  margin-bottom: 16px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease;
}

.book-card:hover img {
  transform: scale(1.05);
}

.book-card h3 {
  font-size: 1.1rem;
  font-weight: bold;
  margin: 0 0 10px 0;
  color: #5c4400;
}

.book-card p {
  font-size: 0.95rem;
  color: #7a6345;
  margin: 0 0 12px 0;
}

.btn {
  display: inline-block;
  margin-top: auto;
  padding: 10px 16px;
  background-color: #b3a78c;
  color: white;
  border: none;
  border-radius: 24px;
  font-size: 0.95rem;
  font-weight: bold;
  text-decoration: none;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 3px 8px rgba(179, 167, 140, 0.4);
}

.btn:hover {
  background-color: #998e77;
  box-shadow: 0 6px 15px rgba(153, 142, 119, 0.6);
}

.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #f3e6d8;
  display: flex;
  justify-content: space-around;
  padding: 10px 0;
  box-shadow: 0 -3px 6px rgba(0, 0, 0, 0.1);
}

.bottom-nav button {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #5c4400;
  cursor: pointer;
  font-weight: 600;
  transition: color 0.25s ease;
}

.bottom-nav button:hover {
  color: #b3a78c;
}

  </style>
</head>
<body>

<header>
  <h1>🔥 Trending Books</h1>
</header>

<section class="section">
  <?php if ($result && $result->num_rows > 0): ?>
    <div class="book-list">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="book-card">
          <img src="<?= htmlspecialchars($row['cover_url']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" />
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p>Author: <?= htmlspecialchars($row['author']) ?></p>
          <a href="bookdetails.php?id=<?= $row['id'] ?>" class="btn">View Details</a>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center; color:#5c4400;">No trending books right now.</p>
  <?php endif; ?>
</section>

<nav class="bottom-nav">
  <button onclick="location.href='index.html'">🏠 Home</button>
  <button onclick="location.href='shelf.php'">🛒 Shelf</button>
  <button onclick="location.href='history.php'">📚 History</button>
</nav>

</body>
</html>



<?php $conn->close(); ?>
