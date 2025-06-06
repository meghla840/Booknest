// Sample Books
const books = [
  { name: 'অপেক্ষা', img: 'images/opekkha.png', pdf: 'pdf/opekkha.pdf', description: 'A story about waiting.', author: 'Author 1' },
  { name: 'পদ্মজা', img: 'images/podda.png', pdf: 'pdf/podda.pdf', description: 'A river\'s story.', author: 'Author 2' },
  { name: 'চরিত্রহীন', img: 'images/chorittrohin.jpg', pdf: 'pdf/chorittrohin.pdf', description: 'Characterless people.', author: 'Author 3' },
  { name: 'পথের পাচালী', img: 'images/pother.png', pdf: 'pdf/pother.pdf', description: 'A journey.', author: 'Author 4' }
];

// Save books to localStorage if not already saved
if (!localStorage.getItem('books')) {
  localStorage.setItem('books', JSON.stringify(books));
}

// Navigation Functions
function goHome() {
  window.location.href = "index.html";
}

function goToMore() {
  window.location.href = "novels.php";
}

function goShelf() {
  window.location.href = "shelf.php";
}

function goBack() {
  window.history.back();
}


  
window.addEventListener('DOMContentLoaded', () => {
    loadRecommendedBooks();
  });
  
function loadRecommendedBooks() {
  fetch('get_recommended_books.php')
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById('recommended-books');
      container.innerHTML = '';

      data.forEach(book => {
        const bookCard = document.createElement('div');
        bookCard.className = 'book-card';
        bookCard.style = 'border: 1px solid #ccc; padding: 10px; width: 180px;';

        const coverImg = document.createElement('img');
        coverImg.src = book.cover_url;
        coverImg.alt = book.title;
        coverImg.style = 'width: 150px; height: 220px; cursor: pointer;';
       
            coverImg.addEventListener('click', () => {
                console.log("Clicked book ID:", book.id);
                goToDetails(book.id);
              });


        const title = document.createElement('strong');
        title.textContent = book.title;

        const author = document.createElement('div');
        author.textContent = `By ${book.author}`;

        const genre = document.createElement('em');
        genre.textContent = book.genre;

        bookCard.appendChild(coverImg);
        bookCard.appendChild(document.createElement('br'));
        bookCard.appendChild(title);
        bookCard.appendChild(document.createElement('br'));
        bookCard.appendChild(author);
        bookCard.appendChild(document.createElement('br'));
        bookCard.appendChild(genre);

        container.appendChild(bookCard);
      });
    })
    .catch(error => {
      console.error('Error loading recommended books:', error);
    });
}


function goToDetails(bookId) {
  window.location.href = `bookdetails.php?book=${encodeURIComponent(bookId)}`;
}



// Add Book to Shelf
function addToShelf(bookName) {
  const books = JSON.parse(localStorage.getItem('books')) || [];
  const shelf = JSON.parse(localStorage.getItem('shelf')) || [];

  const book = books.find(b => b.name === bookName);
  if (!book) return;

  const alreadyAdded = shelf.some(b => b.name === book.name);

  if (!alreadyAdded) {
    shelf.push(book);
    localStorage.setItem('shelf', JSON.stringify(shelf));
    alert('✅ Book added to your shelf!');
  } else {
    alert('⚠️ This book is already in your shelf.');
  }
}

// Load Shelf Books
document.addEventListener('DOMContentLoaded', () => {
  const shelfGrid = document.getElementById('shelf-grid');
  const trendingGrid = document.getElementById('trending-grid');
  const content = document.getElementById('content');
  const urlParams = new URLSearchParams(window.location.search);

  // Shelf page
  if (shelfGrid) {
    const shelf = JSON.parse(localStorage.getItem('shelf')) || [];
    if (shelf.length === 0) {
      shelfGrid.innerHTML = "<p>📚 Your shelf is empty!</p>";
    } else {
      shelfGrid.innerHTML = "";
      shelf.forEach((book, index) => {
        shelfGrid.innerHTML += `
          <div class="book-card">
            <img src="${book.img}" alt="${book.name}">
            <h3>${book.name}</h3>
            <div class="btn-group">
              <button onclick="readBook('${book.pdf}')" class="btn">📖 Read</button>
              <button onclick="deleteFromShelf(${index})" class="btn">🗑️ Delete</button>
            </div>
          </div>
        `;
      });
    }
  }

  // Trending page
  if (trendingGrid) {
    let trending = JSON.parse(localStorage.getItem('trending')) || [];
    const now = Date.now();
    trending = trending.filter(book => now - book.time <= 24 * 60 * 60 * 1000);
    localStorage.setItem('trending', JSON.stringify(trending));

    if (trending.length === 0) {
      trendingGrid.innerHTML = "<p>😔 No trending books right now!</p>";
    } else {
      trending.forEach(book => {
        trendingGrid.innerHTML += `
          <div class="book-card">
            <img src="${book.img}" alt="${book.name}" style="width:100%; height:200px; object-fit:cover;">
            <h3>${book.name}</h3>
            <div class="btn-group">
              <button onclick="readBook('${book.pdf}')" class="btn">📖 Read Online</button>
            </div>
          </div>
        `;
      });
    }
  }

  // Book Details page
  if (document.getElementById('book-title')) {
    const bookName = urlParams.get('book');
    const books = JSON.parse(localStorage.getItem('books')) || [];
    const book = books.find(b => b.name === bookName);

    if (book) {
      document.getElementById('book-image').src = book.img;
      document.getElementById('book-title').textContent = book.name;
      document.getElementById('book-author').textContent = book.author ? `Author: ${book.author}` : '';
      document.getElementById('book-description').textContent = book.description || 'No description available.';
      document.getElementById('download-link').href = book.pdf;
      document.getElementById('read-link').href = `reader.html?pdf=${encodeURIComponent(book.pdf)}`;
    } else {
      document.body.innerHTML = '<p style="text-align:center;">Book not found!</p>';
    }
  }

  // Reader page
  if (content && urlParams.has('pdf')) {
    const pdfPath = urlParams.get('pdf');
    if (pdfPath) {
      const iframe = document.createElement('iframe');
      iframe.src = decodeURIComponent(pdfPath);
      iframe.style.width = "100%";
      iframe.style.height = "90vh";
      iframe.style.border = "none";
      content.appendChild(iframe);
    } else {
      content.innerHTML = "<div class='message'>❌ No PDF found!</div>";
    }
  }
});

// Delete from Shelf
function deleteFromShelf(index) {
  let shelf = JSON.parse(localStorage.getItem('shelf')) || [];
  shelf.splice(index, 1);
  localStorage.setItem('shelf', JSON.stringify(shelf));
  window.location.reload();
}

// Read Book
function readBook(pdfPath) {
  window.location.href = `reader.html?pdf=${encodeURIComponent(pdfPath)}`;
}

//adminpanel
function toggleMenu() {
  const loginButtons = document.getElementById("loginButtons");
  // Toggle the display of the login buttons
  if (loginButtons.style.display === "none" || loginButtons.style.display === "") {
      loginButtons.style.display = "flex";
  } else {
      loginButtons.style.display = "none";
  }
}

// Book list fetch from localStorage
function loadLibrary() {
  let content = document.getElementById('content');
  let books = JSON.parse(localStorage.getItem('books')) || [];

  if (books.length === 0) {
    content.innerHTML = "<h2>No Books Found 📚</h2>";
    return;
  }

  let libraryHTML = `<h2>Library </h2><div class="book-grid">`;
  books.forEach(book => {
    libraryHTML += `
      <div class="book-card">
        <img src="${book.image}" alt="Book">
        <h4>${book.name}</h4>
      </div>
    `;
  });
  libraryHTML += `</div>`;
  content.innerHTML = libraryHTML;
}

// When sidebar button clicked
function showPage(page) {
  let content = document.getElementById('content');

  if (page === 'add-book') {
    content.innerHTML = `
      <div class="card">
        <h2>Add New Book </h2>
        <div class="form-group">
          <label>Book Name</label>
          <input type="text" id="book-name" placeholder="Enter book name">
        </div>
        <div class="form-group">
          <label>Book Image URL</label>
          <input type="text" id="book-image" placeholder="Enter book image URL">
        </div>
        <button class="btn" onclick="addBook()">Add Book</button>
      </div>
    `;
  }
  else if (page === 'library') {
    loadLibrary();  // Library load
  }
  else if (page === 'delete-book') {
    content.innerHTML = `
      <div class="card">
        <h2>Delete Book ❌</h2>
        <div class="form-group">
          <label>Enter Book Name</label>
          <input type="text" id="delete-book-name" placeholder="Enter book name to delete">
        </div>
        <button class="btn" onclick="deleteBook()">Delete Book</button>
      </div>
    `;
  }
  else {
    content.innerHTML = `<h2>Welcome Admin Panel</h2>`;
  }
}

// Add New Book
function addBook() {
  const name = document.getElementById('book-name').value.trim();
  const image = document.getElementById('book-image').value.trim();

  if (!name || !image) {
    alert("Please fill all fields!");
    return;
  }

  const newBook = { name, image };

  let books = JSON.parse(localStorage.getItem('books')) || [];
  books.push(newBook);

  localStorage.setItem('books', JSON.stringify(books));

  alert("Book added successfully!");
  showPage('library');  // Add করার পরে library দেখাবে
}

// Delete Book
function deleteBook() {
  const name = document.getElementById('delete-book-name').value.trim();
  let books = JSON.parse(localStorage.getItem('books')) || [];

  books = books.filter(book => book.name.toLowerCase() !== name.toLowerCase());

  localStorage.setItem('books', JSON.stringify(books));
  alert("Book deleted (if found)!");
  showPage('library');
}

// Default home
showPage('home');

