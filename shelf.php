<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT b.* FROM shelf s JOIN books b ON s.book_id = b.id WHERE s.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$shelfBooks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Shelf</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      background: #fff8f0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #5c4400;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #b3a78c;
      color: #fff;
      padding: 18px 20px;
      text-align: center;
      font-weight: 700;
      font-size: 1.6rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      position: sticky;
      top: 0;
      z-index: 10;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    header button {
      background: transparent;
      border: none;
      color: white;
      font-size: 1.8rem;
      cursor: pointer;
      transition: transform 0.2s ease;
    }
    header button:hover {
      transform: scale(1.2);
    }

    .section {
      padding: 25px 20px;
      max-width: 1100px;
      margin: auto;
    }

   .book-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 24px 20px; /* vertical and horizontal gap */
  justify-items: center;
}

.book-card {
  background: #fef9f4;
  border-radius: 14px;
  box-shadow: 0 6px 14px rgba(179,167,140,0.3);
  padding: 15px 12px 20px 12px;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  cursor: default;
  margin-bottom: 0; /* vertical gap managed by grid gap */
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.35s ease;
  height: 350px; /* fixed height */
  width: 220px;  /* fixed width */
  box-sizing: border-box;
}

.book-card img {
  max-width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 18px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
  user-select: none;
  transition: transform 0.3s ease;
}

.book-card h3 {
  font-size: 1.15rem;
  margin: 0 0 20px 0;
  color: #5c4400;
  font-weight: 700;
  user-select: none;
  flex-grow: 1; /* Push the button to bottom */
}

.book-card button {
  background-color: #b3a78c;
  color: #fff;
  border: none;
  font-weight: 700;
  padding: 10px 18px;
  border-radius: 24px;
  cursor: pointer;
  font-size: 1rem;
  box-shadow: 0 3px 8px rgba(179,167,140,0.4);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.book-card button:hover {
  background-color: #998e77;
  box-shadow: 0 5px 15px rgba(153,142,119,0.7);
}

.book-card:hover {
  transform: scale(1.07);
  box-shadow: 0 16px 30px rgba(179,167,140,0.6);
}

.book-card:hover img {
  transform: scale(1.1);
}

    
    

    .book-card button:hover {
      background-color: #998e77;
      box-shadow: 0 5px 15px rgba(153,142,119,0.7);
    }

    p.empty-msg {
      text-align: center;
      font-size: 1.3rem;
      color: #998e77;
      margin-top: 60px;
      font-weight: 600;
    }

    nav.bottom-nav {
      position: fixed;
      bottom: 0;
      width: 100%;
      background-color: #f3e6d8;
      display: flex;
      justify-content: space-around;
      padding: 10px 0;
      box-shadow: 0 -3px 7px rgba(0,0,0,0.1);
    }

    nav.bottom-nav button {
      background: transparent;
      border: none;
      color: #5c4400;
      font-size: 1.2rem;
      cursor: pointer;
      transition: color 0.25s ease;
      font-weight: 600;
    }

    nav.bottom-nav button:hover {
      color: #b3a78c;
    }

    @media (max-width: 480px) {
      .book-card img {
        height: 180px;
      }
    }

  </style>
</head>
<body>

<header>
  <button onclick="location.href='index.html'" aria-label="Go back">⬅</button>
  My Shelf
</header>

<section class="section">
  <div class="book-grid">
    <?php if (!empty($shelfBooks)): ?>
      <?php foreach ($shelfBooks as $book): ?>
      <div class="book-card">
        <img src="<?= htmlspecialchars($book['cover_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" draggable="false" />
        <h3><?= htmlspecialchars($book['title']) ?></h3>
        <button onclick="location.href='reader.php?pdf=<?= urlencode($book['pdf']) ?>'">Read</button>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="empty-msg">Your shelf is empty.</p>
    <?php endif; ?>
  </div>
</section>

<nav class="bottom-nav" role="navigation" aria-label="Bottom navigation">
  <button onclick="location.href='index.html'">🏠 Home</button>
  <button onclick="location.href='shelf.php'">🛒 Shelf</button>
  <button onclick="location.href='trending.php'">🔥 Trending</button>
</nav>

</body>
</html>
