document.addEventListener("DOMContentLoaded", () => {
  const addButtons = document.querySelectorAll('.btn-add');
  const borrowList = document.getElementById('borrow-list');
  const confirmBtn = document.querySelector('.btn-confirm');

  // Add book to borrow list
  addButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const bookCard = btn.closest('.book-card');
      const title = bookCard.querySelector('strong').innerText;
      const author = bookCard.querySelectorAll('small')[0].innerText;
      const genre = bookCard.querySelectorAll('small')[1].innerText;

      // Create borrow list item
      const li = document.createElement('li');
      li.className = "d-flex justify-content-between align-items-center border-bottom py-2";
      li.innerHTML = `
        <div>
          <strong>${title}</strong><br>
          <small>${author}</small><br>
          <small>${genre}</small>
        </div>
        <button class="btn btn-sm btn-danger btn-remove">Remove</button>
      `;

      // Remove button functionality
      li.querySelector('.btn-remove').addEventListener('click', () => {
        li.remove();
      });

      borrowList.appendChild(li);
    });
  });

  // Confirm button → open confirm modal
  confirmBtn.addEventListener('click', () => {
    const items = borrowList.querySelectorAll('li');
    if (items.length === 0) {
      alert("No books selected!");
      return;
    }

    const books = Array.from(items).map((li) =>
      li.querySelector('strong').innerText
    );

    showConfirmModal(books, () => {
      showBorrowerInfoModal();
    });
  });

  // Show Confirm Modal
  function showConfirmModal(books, onYes) {
    const confirmMessage = document.getElementById("confirmMessage");

    if (books.length === 1) {
      confirmMessage.innerHTML = `Are you sure you want to borrow this book: <strong>"${books[0]}"</strong>?`;
    } else {
      let list = "<ol>";
      books.forEach((title) => (list += `<li>${title}</li>`));
      list += "</ol>";
      confirmMessage.innerHTML = `Are you sure you want to borrow these books: ${list}`;
    }

    const confirmModal = new bootstrap.Modal(document.getElementById("confirmModal"));
    confirmModal.show();

    const yesBtn = document.getElementById("confirmYes");
    yesBtn.onclick = () => {
      confirmModal.hide();
      if (onYes) onYes();
    };
  }

  // Show Borrower Info Modal
  function showBorrowerInfoModal() {
    // Example: fill with dummy data (later can be from form)
    document.getElementById("infoSchoolID").innerText = "2025-0001";
    document.getElementById("infoName").innerText = "Juan Dela Cruz";
    document.getElementById("infoYear").innerText = "3rd Year";
    document.getElementById("infoCourse").innerText = "BSIT - 3A";

    const borrowerModal = new bootstrap.Modal(document.getElementById("borrowerInfoModal"));
    borrowerModal.show();
  }
});
