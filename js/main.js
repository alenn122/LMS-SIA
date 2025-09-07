document.addEventListener("DOMContentLoaded", () => {
  console.log("JS Loaded and DOM Ready");

  const bookLinks = document.querySelectorAll(".book-title");
  const modalOverlay = document.querySelector(".book-modal-overlay");
  const closeBtn = document.querySelector(".close-btn");
  const cartIcon = document.getElementById("cart-icon");
  const cartPanel = document.getElementById("cart-panel");
  const cartList = document.getElementById("cart-list");
  const borrowBooksBtn = document.getElementById("borrow-books-btn");

  // --- MODAL FUNCTIONALITY ---
  const closeModal = () => modalOverlay.style.display = "none";

  // show modal when a book title is clicked
  bookLinks.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      modalOverlay.style.display = "flex";

      //borrow button
      const borrowBtn = modalOverlay.querySelector(".borrow-btn");
      borrowBtn.onclick = () => {
        const bookTitle = modalOverlay.querySelector(".book-info h3").innerText;
        const bookAuthor = modalOverlay.querySelector(".book-info p strong").nextSibling.textContent.trim();
        const bookCover = modalOverlay.querySelector(".book-cover").src;

        const li = document.createElement("li");
        li.innerHTML = `
          <img src="${bookCover}" alt="${bookTitle}" class="book-cover-cart">
          <div class="book-details">
            <h6 class="book-title">${bookTitle}</h6>
            <p class="book-author">${bookAuthor}</p>
          </div>
        `; 

        cartList.appendChild(li);

        // save to local storage for borrower form
        saveCartBooks();

        closeModal();
      };
    });
  });

  // Close modal
  closeBtn.addEventListener("click", closeModal);
  modalOverlay.addEventListener("click", e => {
    if (e.target === modalOverlay) closeModal();
  });

  // --- CART PANEL TOGGLE ---
  cartIcon.addEventListener("click", () => {
    cartPanel.classList.toggle("open");
  });

  // --- BORROW BOOKS BUTTON (Send to borrower form page) ---
  borrowBooksBtn.addEventListener("click", () => {
    saveCartBooks(); // ensure cart is saved
    window.location.href = "borrower_form.html";
  });

  // save cart books to local storage 
  function saveCartBooks() {
    const books = [];
    document.querySelectorAll("#cart-list li").forEach(li => {
      const title = li.querySelector(".book-title").innerText;
      const author = li.querySelector(".book-author").innerText;
      books.push({ title, author });
    });
    localStorage.setItem("cartBooks", JSON.stringify(books));
  }
});
