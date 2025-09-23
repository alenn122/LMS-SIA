    const container = document.getElementById("sidebar-container");
    
    fetch("sidebar.html")
      .then(r => r.text())
      .then(data => {
        const container = document.getElementById("sidebar-container");
        container.innerHTML = data;

        const sidebar = container.querySelector(".sidebar");
        const toggleBtn = container.querySelector("#menuToggle");

        toggleBtn.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
        });
      });

        const links = container.querySelectorAll(".nav-links a");
          links.forEach(link => {
          if (window.location.href.includes(link.getAttribute("href"))) {
            link.classList.add("active");
          }
        });

            
    document.addEventListener("DOMContentLoaded", () => {
      const borrowList = document.getElementById("borrow-list");
      const confirmModal = new bootstrap.Modal(document.getElementById("confirmModal"));
      const successModal = new bootstrap.Modal(document.getElementById("successModal"));

      // ---- SAMPLE DATA ----
      const borrowers = [
        {
          id: "2023001",
          type: "student",
          name: "Juan Dela Cruz",
          year: "3rd Year",
          course: "BSIT - 3A",
          profile: "img/juan.png"
        },
        {
          id: "2023002",
          type: "faculty",
          name: "Prof. Maria Santos",
          department: "Computer Studies",
          profile: "img/maria.png"
        }
      ];

      const idInput = document.getElementById("schoolIdInput");
      const suggestionBox = document.getElementById("suggestionBox");
      const borrowerFields = document.getElementById("borrowerFields");
      const profilePic = document.getElementById("profilePic");
      const nameInput = document.getElementById("nameInput");
      const courseInput = document.getElementById("courseInput");
      const yearInput = document.getElementById("yearInput");

      // Show suggestions when typing
      idInput.addEventListener("input", () => {
        const query = idInput.value.trim();
        suggestionBox.innerHTML = "";
        if (query.length >= 2) {
          const matches = borrowers.filter(b => b.id.includes(query));
          if (matches.length > 0) {
            suggestionBox.style.display = "block";
            matches.forEach(b => {
              const item = document.createElement("button");
              item.className = "list-group-item list-group-item-action d-flex align-items-center";
              item.innerHTML = `
                <img src="${b.profile}" class="rounded-circle me-2" width="35" height="35">
                <div>
                  <strong>${b.name}</strong><br>
                  <small>${b.type === "student" ? b.course + " • " + b.year : b.department}</small>
                </div>
              `;
              item.addEventListener("click", () => {
                borrowerFields.classList.remove("hidden-field");
                profilePic.src = b.profile;
                nameInput.value = b.name;
                if (b.type === "student") {
                  courseInput.value = b.course;
                  yearInput.value = b.year;
                } else {
                  courseInput.value = b.department;
                  yearInput.value = "Faculty Member";
                }
                suggestionBox.style.display = "none";
              });
              suggestionBox.appendChild(item);
            });
          } else {
            suggestionBox.style.display = "none";
          }
        } else {
          suggestionBox.style.display = "none";
        }
      });

      document.addEventListener("click", (e) => {
        if (!suggestionBox.contains(e.target) && e.target !== idInput) {
          suggestionBox.style.display = "none";
        }
      });

      // ADD BOOK BUTTON
      document.querySelectorAll(".btn-add").forEach(btn => {
        btn.addEventListener("click", () => {
          const title = btn.closest(".book-card").querySelector("strong").innerText;
		  const author = btn.closest(".book-card").querySelector("small").innerText;
          const li = document.createElement("li");
			li.innerHTML = `<strong>${title}</strong> by <em>${author}</em> <button class="btn btn-sm btn-danger float-end remove-book">Remove</button>`;
          borrowList.appendChild(li);
          li.querySelector(".remove-book").addEventListener("click", () => li.remove());
        });
      });

      // OPEN CONFIRM MODAL
      document.querySelector(".btn-confirm").addEventListener("click", () => {
        if (borrowerFields.classList.contains("hidden-field"))
          return alert("⚠️ Please select a borrower first!");

        document.getElementById("infoSchoolID").textContent = idInput.value;
        document.getElementById("infoName").textContent = nameInput.value;
        document.getElementById("infoCourse").textContent = courseInput.value;
        document.getElementById("infoYear").textContent = yearInput.value;

        const items = borrowList.querySelectorAll("li");
        document.getElementById("confirmMessage").innerHTML =
          items.length
            ? "<ol>" + Array.from(items).map(li => `<li>${li.querySelector("strong").innerText}</li>`).join("") + "</ol>"
            : "<p>No books selected.</p>";

        confirmModal.show();
      });

      // CONFIRM YES BUTTON
      document.getElementById("confirmYes").addEventListener("click", () => {
        confirmModal.hide();
        successModal.show();
        setTimeout(() => successModal.hide(), 2500);

        idInput.value = "";
        borrowerFields.classList.add("hidden-field");
        borrowList.innerHTML = "";
      });
    });