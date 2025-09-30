<?php include 'db.php'; ?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
$books = [];
$sql = "SELECT * FROM Book LIMIT 10";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
}
?>

<main class="main-content p-4">
    <div class="main-header mb-4">
        <h3>BORROW BOOKS</h3>
    </div>

    <div class="row b-book">
        <!-- Student Info -->
        <div class="col-lg-4">
            <div class="card p-3 shadow-sm">
                <h6 class="mb-3">STUDENT INFORMATION</h6>

                <div class="text-center mb-4">
                    <img id="studentProfilePic" src="img/Pink.jpg" class="rounded-circle" width="120" height="120">
                </div>

                <div class="mb-3">
                    <label class="form-label">STUDENT ID NO.</label>
                    <input type="text" id="studentId" class="form-control" placeholder="Enter student ID">
                    <div id="studentSuggestions" class="list-group"
                        style="display:none; max-height:150px; overflow-y:auto; position:absolute; z-index:1000;">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">NAME</label>
                    <input type="text" id="studentName" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">PROGRAM/STRAND</label>
                    <input type="text" id="studentProgram" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">YEAR LEVEL</label>
                    <input type="text" id="studentYear" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">BORROW DATE</label>
                    <input type="date" id="borrowDate" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">DUE DATE</label>
                    <input type="date" id="dueDate" class="form-control">
                </div>
            </div>
        </div>

        <!-- Book Search and List -->
        <div class="col-lg-8">
            <div class="card p-4 shadow-sm">
                <div class="input-group mb-3 position-relative">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchBox" class="form-control" placeholder="Search (title, author name)" autocomplete="off">
                    <button type="button" id="clearBtn" class="btn btn-outline-secondary" style="display:none;">✖</button>
                    <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                    <div id="suggestions" class="list-group"
                        style="display:none; position:absolute; top:100%; left:0; right:0; z-index:1000; max-height:200px; overflow-y:auto;">
                    </div>
                </div>

                <div id="bookResults">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                            <div>
                                <strong><?php echo htmlspecialchars($book['Title']); ?></strong><br>
                                <small><?php echo htmlspecialchars($book['Author']); ?></small><br>
                                <small><?php echo htmlspecialchars($book['Category']); ?></small>
                            </div>
                            <button type="button" 
                                class="btn btn-sm btn-primary" 
                                onclick="addBook('<?php echo $book['Book_ID']; ?>', '<?php echo htmlspecialchars($book['Title']); ?>')">
                                Add
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h6 class="mt-4">Borrowing List</h6>
                <div id="borrowingList" class="border rounded p-3 mb-3" style="min-height: 100px;"></div>

                <div class="d-flex gap-3">
                    <button class="btn btn-success" id="confirmBtn">CONFIRM</button>
                    <button class="btn btn-danger" id="cancelBtn">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- MODAL CONFIRMATION -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Borrow Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="confirmModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-success" id="confirmYesBtn">Yes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Borrow Recorded</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Borrow details have been successfully recorded.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // ---------- STUDENT INFO ----------
    const studentId = document.getElementById("studentId");
    const studentName = document.getElementById("studentName");
    const studentProgram = document.getElementById("studentProgram");
    const studentYear = document.getElementById("studentYear");
    const studentSuggestions = document.getElementById("studentSuggestions");
    const profilePic = document.getElementById("studentProfilePic");
    const borrowDate = document.getElementById("borrowDate");
    const dueDate = document.getElementById("dueDate");

    const borrowingList = document.getElementById("borrowingList");
    const borrowedBooks = [];

    const confirmBtn = document.getElementById("confirmBtn");
    const confirmYesBtn = document.getElementById("confirmYesBtn");
    const cancelBtn = document.getElementById("cancelBtn");

    const searchBox = document.getElementById("searchBox");
    const clearBtn = document.getElementById("clearBtn");
    const suggestions = document.getElementById("suggestions");
    const bookResults = document.getElementById("bookResults");

    // Student suggestions
    studentId.addEventListener("keyup", function(){
        let query = this.value.trim();
        if(query.length > 0){
            fetch("getStudent.php?q=" + query)
            .then(res=>res.text())
            .then(data=>{
                studentSuggestions.style.display = "block";
                studentSuggestions.innerHTML = data;
            });
        } else studentSuggestions.style.display = "none";
    });

    studentSuggestions.addEventListener("click", function(e){
        if(e.target.classList.contains("suggest-item") || e.target.closest(".suggest-item")){
            const target = e.target.closest(".suggest-item");
            studentId.value = target.dataset.id;
            studentName.value = target.dataset.name;
            studentProgram.value = target.dataset.program;
            studentYear.value = target.dataset.year;
            profilePic.src = target.dataset.pic || "img/Pink.jpg";
            studentSuggestions.style.display = "none";
        }
    });

    // Add book
    window.addBook = function(id,title){
        if(!borrowedBooks.includes(id)) borrowedBooks.push(id);
        const item = document.createElement("div");
        item.className = "d-flex justify-content-between align-items-center border rounded p-2 mb-2";
        item.innerHTML = `<span><strong>${title}</strong></span><button type="button" class="btn btn-sm btn-danger remove-btn">Remove</button>`;
        item.querySelector(".remove-btn").addEventListener("click",()=>{
            borrowedBooks.splice(borrowedBooks.indexOf(id),1);
            item.remove();
        });
        borrowingList.appendChild(item);
    };

    // Clear all inputs
    cancelBtn.addEventListener("click", function(){
        studentId.value = studentName.value = studentProgram.value = studentYear.value = borrowDate.value = dueDate.value = "";
        profilePic.src = "img/Pink.jpg";
        borrowedBooks.length = 0;
        borrowingList.innerHTML = "";
    });

    // Confirm button
    confirmBtn.addEventListener("click", function(){
        if(!studentId.value || borrowedBooks.length===0){
            alert("Please select student and add books.");
            return;
        }
        const detailsHTML = `
            <p><strong>Student ID:</strong> ${studentId.value}</p>
            <p><strong>Name:</strong> ${studentName.value}</p>
            <p><strong>Program:</strong> ${studentProgram.value}</p>
            <p><strong>Year Level:</strong> ${studentYear.value}</p>
            <p><strong>Borrow Date:</strong> ${borrowDate.value}</p>
            <p><strong>Due Date:</strong> ${dueDate.value}</p>
            <p><strong>Books:</strong></p>
            <ul>${Array.from(borrowingList.children).map(b=>`<li>${b.querySelector('span').innerText}</li>`).join('')}</ul>
        `;
        document.getElementById("confirmModalBody").innerHTML = detailsHTML;
        new bootstrap.Modal(document.getElementById("confirmModal")).show();
    });

    // Yes button
    confirmYesBtn.addEventListener("click", function(){
        fetch('saveBorrow.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({
                student_id: studentId.value,
                borrow_date: borrowDate.value,
                due_date: dueDate.value,
                books: borrowedBooks
            })
        }).then(res=>res.json()).then(data=>{
            if(data.success){
                bootstrap.Modal.getInstance(document.getElementById("confirmModal")).hide();
                new bootstrap.Modal(document.getElementById("successModal")).show();
                cancelBtn.click();
            } else alert("Error: "+data.message);
        }).catch(err=>{
            console.error(err); alert("Something went wrong while saving borrow details.");
        });
    });

    // Search bar
    function loadBooks(query = "") {
        let url = "searchbooks.php";
        if(query) url += "?q=" + encodeURIComponent(query);
        fetch(url)
        .then(res => res.json())
        .then(data => {
            bookResults.innerHTML = "";
            if(data.length > 0){
                data.forEach(book=>{
                    const div = document.createElement("div");
                    div.className = "book-card d-flex justify-content-between align-items-center border rounded p-3 mb-2";
                    div.innerHTML = `<div>
                        <strong>${book.Title}</strong><br>
                        <small>${book.Author}</small><br>
                        <small>${book.Category || ""}</small>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="addBook('${book.Book_ID}','${book.Title}')">Add</button>`;
                    bookResults.appendChild(div);
                });
            } else {
                bookResults.innerHTML = "<p class='text-muted'>No books found.</p>";
            }
        });
    }

    // Initial load
    loadBooks();

    searchBox.addEventListener("input", function(){
        const query = this.value.trim();
        if(query.length > 0){
            clearBtn.style.display = "block";
            fetch("searchbooks.php?q=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data=>{
                suggestions.innerHTML = "";
                if(data.length > 0){
                    data.forEach(book=>{
                        const div = document.createElement("div");
                        div.className = "list-group-item list-group-item-action";
                        div.textContent = book.Title + " | " + book.Author;
                        div.dataset.id = book.Book_ID;
                        div.dataset.title = book.Title;
                        div.addEventListener("click", ()=>{
                            searchBox.value = book.Title;
                            suggestions.style.display = "none";
                            clearBtn.style.display = "block";
                            loadBooks(book.Title);
                        });
                        suggestions.appendChild(div);
                    });
                    suggestions.style.display = "block";
                } else {
                    suggestions.innerHTML = "<div class='list-group-item text-muted'>No suggestions</div>";
                    suggestions.style.display = "block";
                }
            });
        } else {
            clearBtn.style.display = "none";
            suggestions.style.display = "none";
            loadBooks();
        }
    });

    clearBtn.addEventListener("click", function(){
        searchBox.value = "";
        clearBtn.style.display = "none";
        suggestions.style.display = "none";
        loadBooks();
        searchBox.focus();
    });

});
</script>
