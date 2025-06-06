<?php
$conn = new mysqli("localhost", "root", "", "booknest_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$book_id = isset($_GET['id']) ? $_GET['id'] : null;
$book = null;

if ($book_id) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Details</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
  <button onclick="goBack()">⬅</button>
  <h1>Book Details</h1>
</header>

<section class="section">
  <?php if ($book): ?>
  <div class="book-details">
    <img src="<?= htmlspecialchars($book['cover_url']) ?>" alt="Book Image">
    <h2><?= htmlspecialchars($book['title']) ?></h2>
    <p>Author: <?= htmlspecialchars($book['author']) ?></p>
    <div class="buttons">
      <button class="btn" onclick="downloadPDFOnly()">Download PDF</button>
      <button class="btn" onclick="AddToShelf()">Add to Shelf</button>
     <a class="btn" href="reader.php?pdf=<?= urlencode($book['pdf']) ?>" onclick="trackReading()">📖 Read Online</a>
      <button class="btn" onclick="addToWishlist()">Add to Wishlist</button>
    </div>
  </div>
  <?php else: ?>
  <div class="error-message">
    <h2>Book Not Found</h2>
    <a href="index.html" class="btn">Go Home</a>
  </div>
  <?php endif; ?>
</section>

<nav class="bottom-nav">
  <button onclick="location.href='index.html'">🏠 Home</button>
  <button onclick="location.href='shelf.php'">🛒 Shelf</button>
  <button onclick="location.href='trending.php'">🔥 Trending</button>
</nav>

<?php if ($book): ?>
<script>
  const book = {
    id: "<?= $book['id'] ?>",
    title: "<?= addslashes($book['title']) ?>",
    author: "<?= addslashes($book['author']) ?>",
    img: "<?= $book['cover_url'] ?>",
    pdf: "<?= $book['pdf'] ?>"
  };

  function goBack() {
    window.history.back();
  }

  function downloadPDFOnly() {
    window.open(book.pdf, '_blank');
  }



function AddToShelf() {
  const formData = new FormData();
  formData.append("book_id", book.id);

  fetch("add_to_shelf.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    console.log("📦 Server response:", data); // Debug log

    const status = data?.status?.trim(); // Remove any space just in case

    if (status === "success") {
      alert("✅ Book added to your shelf!");
      window.location.href = "shelf.php";
    } else if (status === "exists") {
      alert("ℹ️ Book already in your shelf.");
      window.location.href = "shelf.php";
    } else {
      alert("❌ Failed to add book to shelf. Response: " + JSON.stringify(data));
    }
  })
  .catch(error => {
    console.error("❌ Network Error:", error);
    alert("❌ Something went wrong while adding to shelf.");
  });
}

//wishlist 
function addToWishlist() {
  if (!book.id) {
    alert("Book ID is missing!");
    return;
  }

  const formData = new FormData();
  formData.append("book_id", book.id);

  fetch("add_to_wishlist.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin" // important if session cookie needed
  })
  .then(response => response.json())
  .then(data => {
    console.log("Wishlist add response:", data);

    if (data.status === "success") {
      alert("✅ Book added to your Wishlist!");
    } else if (data.status === "exists") {
      alert("ℹ️ Book is already in your Wishlist.");
    } else if (data.status === "error") {
      alert("❌ Error: " + data.message);
    } else {
      alert("❌ Unexpected response");
    }
  })
  .catch(error => {
    console.error("Network error:", error);
    alert("❌ Network error while adding to wishlist");
  });
}




function trackReading() {
  const formData = new FormData();
  formData.append("book_id", book.id);

  fetch("track_read.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      console.log("✅ Reading activity tracked");
    } else {
      console.error("❌ Tracking error:", data.message);
    }
  })
  .catch(error => {
    console.error("❌ Network error while tracking:", error);
  });
}


  function addToHistory(book) {
    let history = JSON.parse(localStorage.getItem('history')) || [];
    const exists = history.some(item => item.name === book.title);
    if (!exists) {
      history.push({
        name: book.title,
        img: book.img,
        path: book.pdf
      });
      localStorage.setItem('history', JSON.stringify(history));
    }
  }

  function addToTrending(book) {
    let trendingBooks = JSON.parse(localStorage.getItem('trendingBooks')) || [];
    const exists = trendingBooks.some(item => item.name === book.title);
    if (!exists) {
      trendingBooks.push({
        name: book.title,
        img: book.img,
        path: book.pdf,
        timestamp: Date.now()
      });
      localStorage.setItem('trendingBooks', JSON.stringify(trendingBooks));
      setTimeout(() => removeFromTrending(book.title), 180000); // 3 minutes
    }
  }

  function removeFromTrending(title) {
    let trendingBooks = JSON.parse(localStorage.getItem('trendingBooks')) || [];
    trendingBooks = trendingBooks.filter(item => item.name !== title);
    localStorage.setItem('trendingBooks', JSON.stringify(trendingBooks));
  }
  console.log("Book ID is:", book.id);

</script>
<?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>