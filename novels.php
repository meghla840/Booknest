<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booknest_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title, author, genre,cover_url,pdf FROM books";
$result = $conn->query($sql);

$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>BookNest - Novels</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<header>
  <button onclick="goHome()">⬅</button>
  <h1>Books Bucket</h1>
  <button onclick="goShelf()">🛒</button>
</header>

<section class="section">
  <div class="book-grid">
    <?php if (count($books) > 0): ?>
      <?php foreach ($books as $book): ?>
        <a href="bookdetails.php?id=<?php echo urlencode($book['id']); ?>" class="book-link">
          <div>
            <img src="<?php echo htmlspecialchars($book['cover_url'], ENT_QUOTES); ?>" alt="<?php 
            echo htmlspecialchars($book['title'], ENT_QUOTES); ?>" />
            <p><?php echo htmlspecialchars($book['title']); ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No books found.</p>
    <?php endif; ?>
  </div>
</section>

<nav class="bottom-nav">
  <button onclick="goHome()">🏠 Home</button>
  <button onclick="goTrending()">🔥 Trending</button>
  <button onclick="goShelf()">📚 Shelf</button>
</nav>

<script>
  function goHome() {
    window.location.href = 'index.html';
  }
  function goTrending() {
    window.location.href = 'trending.php';
  }
  function goShelf() {
    window.location.href = 'shelf.php';
  }
  function goToDetails(bookId) {
    window.location.href = 'bookdetails.php?book=' + encodeURIComponent(bookId);
  }
</script>
</body>
</html>

<?php
$conn->close();
?>