document.addEventListener("DOMContentLoaded", () => {
  const studentIdInput = document.getElementById("student-id");

  const studentForm = document.getElementById("student-form");
  const facultyForm = document.getElementById("faculty-form");

  const borrowedBooksStudent = document.getElementById("borrowed-books");
  const borrowedBooksFaculty = document.getElementById("borrowed-books-faculty");

  const confirmStudentBtn = document.getElementById("confirm-borrow-student");
  const confirmFacultyBtn = document.getElementById("confirm-borrow-faculty");

  // Modal elements
  const confirmModal = document.getElementById("confirm-modal");
  const closeModalBtn = document.getElementById("close-modal");

  // Mock data
  const students = {
    "2022-1234-56": { name: "Jane Dela Cruz", course: "BSIT", section: "1-41" }
  };

  const faculty = {
    "F-9876-12": { name: "Dr. John Smith", role: "Instructor – CS Dept" }
  };

  // show form based on ID input
  studentIdInput.addEventListener("input", e => {
    const id = e.target.value.trim();

    // clear previous forms
    studentForm.classList.add("d-none");
    facultyForm.classList.add("d-none");
    borrowedBooksStudent.innerHTML = "";
    borrowedBooksFaculty.innerHTML = "";

    //  student form
    if (students[id]) {
      studentForm.classList.remove("d-none");
      document.getElementById("student-name").value = students[id].name;
      document.getElementById("course").value = students[id].course;
      document.getElementById("section").value = students[id].section;
      loadBorrowedBooks(borrowedBooksStudent);

    } 
    //  faculty form
    else if (faculty[id]) {
      facultyForm.classList.remove("d-none");
      document.getElementById("faculty-name").value = faculty[id].name;
      document.getElementById("faculty-role").value = faculty[id].role;
      loadBorrowedBooks(borrowedBooksFaculty);
    }
  });

  // load borrowed books from local storage
  function loadBorrowedBooks(listElement) {
    const cartBooks = JSON.parse(localStorage.getItem("cartBooks")) || [];
    cartBooks.forEach(book => {
      const li = document.createElement("li");
      li.className = "list-group-item d-flex flex-column align-items-start";
      li.innerHTML = `
        <strong>${book.title}</strong><br>
        <small>${book.author}</small>
      `;
      listElement.appendChild(li);
    });
  }

  const showModal = () => confirmModal.classList.remove("d-none");

  const closeModal = () => {
    confirmModal.classList.add("d-none");
    localStorage.removeItem("cartBooks"); // Clear cart after borrow
    window.location.href = "searchbook.html"; // Redirect
  };

  // Confirm borrow buttons
  confirmStudentBtn.addEventListener("click", showModal);
  confirmFacultyBtn.addEventListener("click", showModal);

  // Close modal button
  closeModalBtn.addEventListener("click", closeModal);
});
