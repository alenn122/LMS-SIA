<?php

// ----------------------
// RETURN BOOKS HANDLER (AJAX)
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'returnBooks') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "lms_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => $conn->connect_error]));
    }

    $user_id   = intval($_POST['user_id']);
    $user_type = $_POST['user_type']; // 'student' or 'faculty'
    $book_ids  = $_POST['book_ids'];  // array from JS

    // prepare statements
    $updateBorrow = $conn->prepare("
        UPDATE borrow_record
        SET Status='returned', Return_Date=NOW()
        WHERE User_ID=? AND User_Type=? AND Book_ID=? AND Status='borrowed'
    ");
    $updateBook = $conn->prepare("
        UPDATE book 
        SET Available_Copies = Available_Copies+1,
            Borrow_Copies = IF(Borrow_Copies>0, Borrow_Copies-1,0)
        WHERE Book_ID=?
    ");

    foreach ($book_ids as $book_id) {
        $book_id = intval($book_id);
        $updateBorrow->bind_param("isi", $user_id, $user_type, $book_id);
        $updateBorrow->execute();

        $updateBook->bind_param("i", $book_id);
        $updateBook->execute();
    }

    echo json_encode(["status" => "success"]);
    exit;
}

// ----------------------
// DATABASE CONNECTION
// ----------------------
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "lms_db";

// OOP connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ----------------------
// FETCH STUDENTS
// ----------------------
$students = [];
$result = mysqli_query($conn, "SELECT Student_ID, Name, School_ID_Number, Course, Year_Level FROM Students");
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = [
        'id' => $row['School_ID_Number'],
        'type' => 'student',
        'name' => $row['Name'],
        'program' => $row['Course'],
        'year' => $row['Year_Level'],
        'borrowedBooks' => [] // will fill next
    ];
}

// ----------------------
// FETCH FACULTY
// ----------------------
$faculty = [];
$result = mysqli_query($conn, "SELECT Faculty_ID, Name, School_ID_Number, Department, Designation FROM Faculty");
while ($row = mysqli_fetch_assoc($result)) {
    $faculty[] = [
        'id' => $row['School_ID_Number'],
        'type' => 'faculty',
        'name' => $row['Name'],
        'program' => $row['Department'],
        'year' => $row['Designation'],
        'borrowedBooks' => [] // will fill next
    ];
}

// ----------------------
// FETCH BORROWED BOOKS
// ----------------------
$sql = "
    SELECT br.User_Type, br.User_ID, b.Title, b.Author, br.Borrow_Date, br.Status
    FROM Borrow_Record br
    JOIN Book b ON br.Book_ID = b.Book_ID
";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $bookData = [
        'title' => $row['Title'],
        'author' => $row['Author'],
        'date' => $row['Borrow_Date'],
        'status' => $row['Status']
    ];

    // Assign borrowed books to the correct borrower
    if ($row['User_Type'] === 'student') {
        foreach ($students as &$s) {
            if ($s['id'] == $row['User_ID']) {
                $s['borrowedBooks'][] = $bookData;
                break;
            }
        }
    } else { // faculty
        foreach ($faculty as &$f) {
            if ($f['id'] == $row['User_ID']) {
                $f['borrowedBooks'][] = $bookData;
                break;
            }
        }
    }
}

// ----------------------
// MERGE STUDENTS + FACULTY
// ----------------------
$borrowers = array_merge($students, $faculty);
$borrowers_json = json_encode($borrowers);

// Close connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- FAVICON -->
  <link rel="shortcut icon" href="/img/loa_logo.png" type="image/x-icon">
  <!-- BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FONT AWESOME -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
  <!-- ADMIN CSS -->
  <link rel="stylesheet" href="admin.css">

  <style>
    .main-header {
      margin-top: 90px;
      background-color: #e9eef6;
      text-align: center;
      padding: 30px;
      font-weight: bold;
      margin-bottom: 15px;
      font-size: 1.5rem;
    }

    #sidebar {
      width: 250px;            /* width when open */
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      transition: width .3s;
    }

      #sidebar.collapsed {
        width: 60px;             /* width when collapsed */
      }

      .content-container {
        margin-left: 250px;       /* matches sidebar width */
        transition: margin-left .3s;
      }

      #sidebar.collapsed ~ .content-container {
        margin-left: 60px;        /* matches collapsed width */
      }

    .b-book {
      margin-left: auto;
      margin-right: auto;
    }

    .btn-view {
      background-color: #0d3b8c;
      color: white;
      font-weight: 500;
    }

    .btn-return {
      background-color: #0d3b8c;
      color: #fff;
      font-weight: bold;
      width: 200px;
      border-radius: 8px;
    }

    .btn-reset {
      background-color: #f0f3f8;
      color: #0d3b8c;
      font-weight: bold;
      width: 120px;
      border-radius: 8px;
    }

    table thead {
      background-color: #f0f3f8;
      font-weight: bold;
    }

    #suggestionBox {
      border-radius: 6px;
      max-height: 200px;
      overflow-y: auto;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    #suggestionBox button {
      border: none;
      background: white;
      text-align: left;
      transition: background-color 0.2s;
    }

    #suggestionBox button:hover {
      background-color: #f8f9fa;
    }

    /* MODAL STYLES */
    .modal-content {
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .modal-body h5 {
      font-weight: bold;
    }
  </style>

  <title>RETURN BOOKS</title>
</head>

<body>
  <header>
    <div class="logo"><img src="/img/loa_logo.png" alt=""></div>
    <div class="header-text">
      COLEGIO DE SAN PEDRO <br>
      Phase 1A, Pacita Complex, 1, City Of San Pedro, 4023 Laguna
    </div>
  </header>

  <!-- SIDEBAR 
  <div id="sidebar-container"></div>
  -->
    <?php include 'sidebar.html';?>
  <div class="content-container">
  <div class="main-header">RETURN BOOKS</div>
  <div class="container m-auto">
    <div class="row">
      <div class="col-md-8 p-0 b-book">

        <!-- SCHOOL ID INPUT + SUGGESTIONS -->
        <div class="d-flex mb-3 position-relative">
          <input id="schoolIdInput" type="text" class="form-control me-2" placeholder="SCHOOL ID NO.">

          <div id="suggestionBox" 
            class="list-group position-absolute w-100 z-3 bg-white shadow border rounded"
            style="display:none; top: 100%; left: 0;">
          </div>
        </div>

        <!-- STUDENT DETAILS -->
        <div id="studentFields" class="row mb-3 d-none">
          <div class="col-md-4">
            <label for="studentName" class="form-label">Student Name</label>
            <input id="studentName" type="text" class="form-control" placeholder="Juan Dela Cruz" readonly>
          </div>
          <div class="col-md-4">
            <label for="studentProgram" class="form-label">Program / Strand</label>
            <input id="studentProgram" type="text" class="form-control" placeholder="BSIT" readonly>
          </div>
          <div class="col-md-4">
            <label for="studentYear" class="form-label">Year Level</label>
            <input id="studentYear" type="text" class="form-control" placeholder="3" readonly>
          </div>
        </div>


        <!-- FACULTY DETAILS -->
        <div id="facultyFields" class="row mb-3 d-none">
          <div class="col-md-4">
            <label for="facultyName" class="form-label">Faculty Name</label>
            <input id="facultyName" type="text" class="form-control" placeholder="Prof. Maria Santos" readonly>
          </div>
          <div class="col-md-4">
            <label for="facultyDept" class="form-label">Department</label>
            <input id="facultyDept" type="text" class="form-control" placeholder="Computer Science Dept." readonly>
          </div>
          <div class="col-md-4">
            <label for="facultyPosition" class="form-label">Position / Designation</label>
            <input id="facultyPosition" type="text" class="form-control" placeholder="Faculty" readonly>
          </div>
        </div>


        <!-- BOOKS TABLE -->
        <div id="booksTableWrapper" class="table-responsive mb-4 d-none">
          <table class="table align-middle">
            <thead>
              <tr>
                <th></th>
                <th>BOOK TITLE</th>
                <th>AUTHOR</th>
                <th>STATUS</th>
                <th>BORROW DATE</th>
              </tr>
            </thead>
            <tbody id="booksTableBody"></tbody>
          </table>
        </div>

        <div class="d-flex justify-content-center gap-3">
          <button id="returnBtn" class="btn btn-return" disabled>RETURN SELECTED BOOKS</button>
          <button class="btn btn-reset" onclick="resetForm()">RESET</button>
        </div>
      </div>
    </div>
  </div>
  </div>

  <!-- CONFIRM RETURN MODAL -->
<!-- RETURN SELECTED BOOKS MODAL -->
<div class="modal fade" id="returnConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Modal header with blue background -->
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title mb-0">Confirm Return</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <p class="fw-semibold mb-2">Please check if the information is correct:</p>

        <!-- Borrower info card -->
        <div class="border rounded p-3 mb-3">
          <p class="mb-1"><strong>School ID:</strong> <span id="studentId">2023001</span></p>
          <p class="mb-1"><strong>Name:</strong> <span id="studentName">Juan Dela Cruz</span></p>
          <p class="mb-1"><strong>Course / Dept:</strong> <span id="studentCourse">BSIT - 3A</span></p>
          <p class="mb-0"><strong>Year / Status:</strong> <span id="studentStatus">3rd Year</span></p>
        </div>

        <p class="fw-semibold mb-2">Books to Return:</p>

        <!-- Books list -->
        <div class="border rounded p-3 mb-3">
          <ul id="returnBookList" class="list-unstyled mb-0">
            <!-- JS will append list items here -->
            <li>Book Title 1</li>
            <li>Book Title 2</li>
          </ul>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="confirmReturnBtn">Yes</button>
      </div>
    </div>
  </div>
</div>


  <!-- SUCCESS MODAL -->
  <div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center">
        <div class="modal-body p-5">
          <i class="fa-solid fa-circle-check text-success" style="font-size: 3rem;"></i>
          <h5 class="mt-3">Books Returned Successfully!</h5>
          <p class="text-muted">Return recorded. See logs/history for review.</p>
        </div>
      </div>
    </div>
  </div>

<!-- add this just before your closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const borrowers = <?php echo $borrowers_json; ?>;

const idInput = document.getElementById("schoolIdInput");
const suggestionBox = document.getElementById("suggestionBox");
const booksTableWrapper = document.getElementById("booksTableWrapper");
const booksTableBody = document.getElementById("booksTableBody");
const returnBtn = document.getElementById("returnBtn");
let selectedBorrower = null;

idInput.addEventListener("input", () => {
  const query = idInput.value.trim();
  suggestionBox.innerHTML = "";
  if (query.length >= 2) {
    const matches = borrowers.filter(b => b.id.includes(query));
    if (matches.length > 0) {
      suggestionBox.style.display = "block";
      matches.forEach(b => {
        const item = document.createElement("button");
        item.type = "button";
        item.className = "list-group-item list-group-item-action d-flex align-items-center";
        item.innerHTML = `
          <img src="${b.profile}" class="rounded-circle me-2" width="35" height="35">
          <div>
            <strong>${b.name}</strong><br>
            <small>${b.type === "student" ? b.program + " • " + b.year : b.program}</small>
          </div>
        `;
        item.addEventListener("click", () => selectBorrower(b));
        suggestionBox.appendChild(item);
      });
    } else {
      suggestionBox.style.display = "none";
    }
  } else {
    suggestionBox.style.display = "none";
  }
});

function selectBorrower(b) {
  selectedBorrower = b;
  booksTableWrapper.classList.remove("d-none");
  suggestionBox.style.display = "none";

  // Hide all fields first
  document.getElementById("studentFields").classList.add("d-none");
  document.getElementById("facultyFields").classList.add("d-none");

  if (b.type === "student") {
    const studentFields = document.getElementById("studentFields");
    studentFields.classList.remove("d-none");
    document.getElementById("studentName").value = b.name;
    document.getElementById("studentProgram").value = b.program;
    document.getElementById("studentYear").value = b.year;
  } else {
    const facultyFields = document.getElementById("facultyFields");
    facultyFields.classList.remove("d-none");
    document.getElementById("facultyName").value = b.name;
    document.getElementById("facultyDept").value = b.program;
    document.getElementById("facultyPosition").value = b.year;
  }

  // Fill books table
  booksTableBody.innerHTML = "";
  b.borrowedBooks.forEach((book, i) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td><input type="checkbox" class="book-checkbox" data-index="${i}"></td>
      <td>${book.title}</td>
      <td>${book.author}</td>
      <td>${book.status}</td>
      <td>${book.date}</td>
    `;
    booksTableBody.appendChild(row);
  });

  // Enable/disable return button on checkbox change
  const checkboxes = booksTableBody.querySelectorAll(".book-checkbox");
  checkboxes.forEach(cb => {
    cb.addEventListener("change", () => {
      returnBtn.disabled = !booksTableBody.querySelector(".book-checkbox:checked");
    });
  });
}

returnBtn.addEventListener("click", () => {
  // collect selected books
  const selectedBooks = [...booksTableBody.querySelectorAll(".book-checkbox:checked")]
    .map(cb => selectedBorrower.borrowedBooks[cb.dataset.index]);

  // fill modal list
  const list = document.getElementById("returnBookList");
  list.innerHTML = "";
  selectedBooks.forEach(book => {
    const li = document.createElement("li");
    li.className = "list-group-item";
    li.textContent = `${book.title} by ${book.author}`;
    list.appendChild(li);
  });

  // save selectedBooks for confirm click
  window.booksToReturn = selectedBooks; // ✅ ADDED

  // show the confirm modal
  const modal = new bootstrap.Modal(document.getElementById("returnConfirmModal"));
  modal.show();
});

// ✅ ADDED: Actually send POST to PHP when confirm clicked
document.getElementById("confirmReturnBtn").addEventListener("click", () => {
  if (!window.booksToReturn || window.booksToReturn.length === 0) return;

  const bookIds = window.booksToReturn.map(b => b.Book_ID); // our PHP expects Book_ID

  fetch('return-books.php', { // same file
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({
      action: 'returnBooks',
      user_id: selectedBorrower.id,
      user_type: selectedBorrower.type,
      'book_ids[]': bookIds // PHP gets array
    })
  })
  .then(r => r.json())
  .then(res => {
    console.log(res);
    if (res.status === 'success') {
      // hide confirm modal
      const modalEl = document.getElementById("returnConfirmModal");
      const modal = bootstrap.Modal.getInstance(modalEl);
      modal.hide();

      // show success modal if you have one
      if (document.getElementById("successModal")) {
        new bootstrap.Modal(document.getElementById("successModal")).show();
      }

      // reload page or refresh borrowers array
      location.reload();
    }
  })
  .catch(err => console.error(err));
});

function resetForm() {
  idInput.value = "";
  document.getElementById("studentFields").classList.add("d-none");
  document.getElementById("facultyFields").classList.add("d-none");
  booksTableWrapper.classList.add("d-none");
  booksTableBody.innerHTML = "";
  returnBtn.disabled = true;
}
</script>


</body>
</html>