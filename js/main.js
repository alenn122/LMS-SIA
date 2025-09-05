document.addEventListener("DOMContentLoaded", () => {
  console.log("JS Loaded and DOM Ready");

  const bookLinks = document.querySelectorAll(".book-title");
  const modalOverlay = document.querySelector(".book-modal-overlay");
  const closeBtn = document.querySelector(".close-btn");

  if (!modalOverlay) {
    console.error("Modal overlay not found!");
    return;
  }

  bookLinks.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      console.log("Book clicked");
      modalOverlay.style.display = "flex"; // show modal
    });
  });

  closeBtn.addEventListener("click", () => {
    modalOverlay.style.display = "none"; // hide modal
  });

  modalOverlay.addEventListener("click", e => {
    if (e.target === modalOverlay) {
      modalOverlay.style.display = "none"; // close on outside click
    }
  });
});
