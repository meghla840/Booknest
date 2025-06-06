<?php
session_start();
$conn = new mysqli("localhost", "root", "", "booknest_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'] ?? null;

$wishlist_books = [];

if ($user_id) {
    $sql = "SELECT b.id, b.title, b.author, b.cover_url, b.pdf 
            FROM wishlist w
            JOIN books b ON w.book_id = b.id
            WHERE w.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $wishlist_books[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Wishlist</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    /* Example shelf-like card design */
    .wishlist-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 20px;
      padding: 15px;
    }
    .book-card {
      background: #f9f4ee;
      border-radius: 10px;
      padding: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.2s ease;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 12px rgba(0,0,0,0.15);
    }
    .book-card img {
      width: 120px;
      height: 180px;
      border-radius: 6px;
      object-fit: cover;
      margin-bottom: 10px;
    }
    .book-card h3 {
      font-size: 1rem;
      margin: 5px 0;
      text-align: center;
      color: #5c4400;
    }
    .book-card p.author {
      font-size: 0.85rem;
      color: #998e77;
      margin: 0 0 10px 0;
    }
    .btn-group {
      margin-top: auto;
    }
    .btn {
      background-color: #b3a78c;
      color: #fff;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    .btn:hover {
      background-color: #998e77;
    }
  </style>
</head>
<body>

<header>
  <button onclick="goBack()">⬅</button>
  <h1>My Wishlist</h1>
</header>

<section class="section" id="wishlist-section">
  <?php if (!$user_id): ?>
    <p>Please <a href="login.php">login</a> to view your wishlist.</p>
  <?php elseif (empty($wishlist_books)): ?>
    <p>😔 No books in your Wishlist!</p>
  <?php else: ?>
    <div class="wishlist-grid">
      <?php foreach ($wishlist_books as $book): ?>
        <div class="book-card">
          <img src="<?= htmlspecialchars($book['cover_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" />
          <h3><?= htmlspecialchars($book['title']) ?></h3>
          <p class="author"><?= htmlspecialchars($book['author']) ?></p>
          <div class="btn-group">
            <a href="reader.php?pdf=<?= urlencode($book['pdf']) ?>" class="btn">📖 Read Online</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<nav class="bottom-nav">
  <button onclick="location.href='index.html'">🏠 Home</button>
  <button onclick="location.href='shelf.php'">🛒 Shelf</button>
  <button onclick="location.href='trending.php'">🔥 Trending</button>
</nav>

<script>
  function goBack() {
    window.history.back();
  }
</script>

</body>
</html>

<?php $conn->close(); ?>
