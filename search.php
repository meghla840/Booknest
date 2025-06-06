<?php
session_start();
// search.php

// Database setup
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'booknest_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search term from URL
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

$sql = "SELECT id, title, author, genre, cover_url FROM books WHERE title LIKE '%$search%'";
$result = $conn->query($sql);

// Log genres for logged-in users
if (isset($_SESSION['user_id']) && $result && $result->num_rows > 0) {
    $userId = $_SESSION['user_id'];
    $loggedGenres = [];

    while ($row = $result->fetch_assoc()) {
        $genre = $conn->real_escape_string($row['genre']);
        if (!in_array($genre, $loggedGenres)) {
            $loggedGenres[] = $genre;

            $stmt = $conn->prepare("INSERT INTO user_search_history (user_id, genre) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $genre);
            $stmt->execute();
            $stmt->close();
        }
    }

    $result->data_seek(0); // Reset result pointer
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Search Results - BookNest</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<header>
  <h1>BookNest</h1>
  <div class="header-icons">
    <form action="search.php" method="GET" class="search-form">
      <input type="text" name="q" placeholder="Search books by name..." 
      value="<?php echo htmlspecialchars($search); ?>" required />
      <button type="submit">🔍</button>
    </form>
    <button onclick="location.href='shelf.php'">🛒</button>
    <button onclick="location.href='profile.php'">👤</button>
  </div>
</header>

<h2 style="text-align: center; margin: 20px 0;">Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>

<?php
if ($result && $result->num_rows > 0) {
    echo "<div class='book-grid'>";  // book-grid class from your css for grid style
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $title = htmlspecialchars($row['title']);
        $author = htmlspecialchars($row['author']);
        $genre = htmlspecialchars($row['genre']);
        $cover = htmlspecialchars($row['cover_url']);

       echo "<div class='book-card'>";
        echo "<a href='bookdetails.php?id=$id' class='book-link'>";
        echo "<img src='$cover' alt='$title' class='book-cover'>";
        echo "<strong>$title</strong>";
        echo "</a>";
        echo "<p>By $author</p>";
        echo "<em>$genre</em>";
        echo "</div>";

    }
    echo "</div>";
} else {
    echo "<p>No books found.</p>";
}
$conn->close();
?>

<nav class="bottom-nav">
  <button onclick="location.href='index.html'">🏠 Home</button>
  <button onclick="location.href='shelf.php'">🛒 Shelf</button>
  <button onclick="location.href='trending.php'">🔥 Trending</button>
</nav>

</body>
</html>
