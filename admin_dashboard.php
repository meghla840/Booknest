<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard - Booknest</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f3e6d8;
    }
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 70px;
      height: 100%;
      background: #382c13;
      padding: 20px 0;
      transition: width 0.4s ease;
      z-index: 100;
    }
    .sidebar.expanded {
      width: 230px;
    }
    .sidebar .menu-btn {
      color: #fff;
      font-size: 24px;
      text-align: center;
      cursor: pointer;
      margin-bottom: 30px;
      transition: transform 0.3s;
    }
    .sidebar .menu-btn:hover {
      transform: rotate(90deg);
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar ul li {
      padding: 15px 25px;
      color: #fff;
      cursor: pointer;
      margin-bottom: 10px;
      border-radius: 10px;
      transition: background 0.3s, padding-left 0.3s;
      font-size: 16px;
      white-space: nowrap;
    }
    .sidebar ul li:hover {
      background: rgba(255, 255, 255, 0.2);
      padding-left: 35px;
    }

    .main {
      margin-left: 70px;
      transition: margin-left 0.4s ease;
      padding: 20px;
    }
    .sidebar.expanded ~ .main {
      margin-left: 230px;
    }

    .topbar {
      background: #ffffff;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      border-radius: 10px;
      margin-bottom: 20px;
    }
    .profile {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: url('https://th.bing.com/th/id/OIP.eXGv6QroARkcxQEYwWZjIAHaHu?rs=1&pid=ImgDetMain') no-repeat center/cover;
    }

    .card {
      background: #ffffff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    .btn {
      background: #d5bf8f;
      color: white;
      padding: 10px 25px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 15px;
    }
    .btn:hover {
      background: #241a02;
    }

    .book-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
    }
    .book-card {
      background: #fff;
      padding: 10px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }
    .book-card:hover {
      transform: translateY(-5px);
    }
    .book-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
    }
    .book-card h4 {
      margin-top: 10px;
      font-size: 16px;
    }
  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <div class="menu-btn" onclick="toggleSidebar()">☰</div>
    <ul>
      <li onclick="window.location.href='index.html'">Home</li>
      <li onclick="showPage('add-book')">Add Book</li>
      <li onclick="showPage('delete-book')">Delete Book</li>
   
      <li onclick="showPage('user-interface')">User Interface</li>
    </ul>
  </div>

  <div class="main" id="main">
    <div class="topbar">
      <div class="search-box" style="font-size: 20px; font-weight: 600;">Admin Panel</div>
      <div class="profile"></div>
    </div>
    <?php
      if (isset($_GET['msg'])) {
          $msg = $_GET['msg'];
          $alert = "";

          if ($msg == "deleted") {
              $alert = "Book deleted successfully.";
          } elseif ($msg == "error") {
              $alert = "Error deleting book.";
          } elseif ($msg == "invalid") {
              $alert = "Invalid request.";
          }

          if ($alert) {
              echo "<div style='background-color:#d4edda; color:#155724; padding:10px; margin:10px 20px; border-radius:5px;'>$alert</div>";
          }
      }
    ?>

    <div id="content"></div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('expanded');
      document.getElementById('main').classList.toggle('expanded');
    }

    function showPage(page) {
      const content = document.getElementById('content');

      if (page === 'add-book') {
        content.innerHTML = `
          <div class="card">
            <h2>Add New Book</h2>
            <form action="add_book.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <label>Book ID</label>
                <input type="text" name="id" required>
              </div>
              <div class="form-group">
                <label>Book Name</label>
                <input type="text" name="book_name" required>
              </div>
              <div class="form-group">
                <label>Author Name</label>
                <input type="text" name="author_name" required>
              </div>
              <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" required>
              </div>
              <div class="form-group">
                <label>Book Image</label>
                <input type="file" name="book_image" accept="image/*" required>
              </div>
              <div class="form-group">
                <label>Book PDF</label>
                <input type="file" name="book_pdf" accept="application/pdf" required>
              </div>
              <button class="btn" type="submit">Add Book</button>
            </form>
          </div>
        `;
      } else if (page === 'delete-book') {
        content.innerHTML = `
          <div class="card">
            <h2>Delete Book ❌</h2>
            <form action="delete_book.php" method="POST">
              <div class="form-group">
                <label>Enter Book ID</label>
                <input type="text" name="id" required>
              </div>
              <button class="btn" type="submit">Delete Book</button>
            </form>
          </div>
        `;
      }else if (page === 'user-interface') {
        fetch('get_users.php')
          .then(res => res.json())
          .then(users => {
            let html = `<h2>Logged-in Users</h2><div style="display:flex;flex-wrap:wrap;gap:20px;">`;
            users.forEach(user => {
              html += `
                <div style="width:200px;">
                  <a href="view_user_profile.php?id=${user.id}" style="text-decoration:none;color:inherit;">
                    <div style="padding:10px;border-radius:10px;background:#fff;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                      <img src="${user.profilePic || 'https://example.com/default.jpg'}" style="width:60px;height:60px;border-radius:50%;"><br>
                      <strong>${user.name}</strong><br>
                      <small>${user.email}</small><br><br>
                    </div>
                  </a>
                  <form onsubmit="event.preventDefault(); blockUser('${user.email}')">
                          <button class="btn" type="submit" style="margin-top:5px;width:100%;">
                            ${user.is_blocked ? 'Unblock' : 'Block'}
                          </button>
                        </form>

                </div>`;
            });
            html += `</div>`;
            content.innerHTML = html;
          })
          .catch(() => {
            content.innerHTML = `<div class="card"><p>Could not load users.</p></div>`;
          });
      } else {
        content.innerHTML = `<div class="card"><p>Page not found.</p></div>`;
      }
    }

    // Block user function using AJAX
    function blockUser(email) {
      fetch('block_user.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=' + encodeURIComponent(email)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert(data.success);
          showPage('user-interface'); // Refresh user list after blocking
        } else {
          alert(data.error || 'Something went wrong.');
        }
      })
      .catch(err => {
        alert("Server error.");
        console.error(err);
      });
    }

    // Default page load
    showPage('Home');
  </script>
</body>
</html>
