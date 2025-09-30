<?php
require_once '../connection.php';
require_once '../session_auth.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- FAVICON -->
    <link rel="shortcut icon" href="/img/loa_logo.png" type="image/x-icon">
    <!-- BS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- FA -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- ADMIN CSS -->
    <link rel="stylesheet" href="admin.css">
    <title>ADMIN</title>
    <style>
        .main-header {
            margin-top: 120px;
            background-color: #e9eef6;
            text-align: center;
            padding: 30px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .dashboard-container {
            margin-left: 240px;
        }

        .stat-card {
            background: #f9fafc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .reports-card {
            background: #f9fafc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .table thead {
            background: #e6ebf5;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo"><img src="/img/loa_logo.png" alt=""></div>

        <div class="header-text">
            COLEGIO DE SAN PEDRO <br>
            UNDER THE NEW MANAGEMENT OF LYCEUM OF ALABANG
            Phase 1A, Pacita Complex, 1, City Of San Pedro, 4023 Laguna
        </div>
    </header>

    <!-- NAVIGATION -->
    <div id="sidebar-container"></div>


    <!-- MAIN CONTENT -->
        <div class="main-header mb-5">DASHBOARD</div>
        <div class="container m-auto">
            <div class="dashboard-container">
                <div class="text-center mb-4">
                    <p>👋 Welcome, Librarian!</p>
                    <small class="text-muted">Here’s what’s happening in the library today:</small>
                </div>

                <!-- TOP -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">10</div>
                            <div class="fw-bold">Books Borrowed</div>
                            <small class="text-muted">Total borrowed books for the current day</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">7</div>
                            <div class="fw-bold">Books Due</div>
                            <small class="text-muted">Books expected to be returned</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">5</div>
                            <div class="fw-bold">Overdue Books</div>
                            <small class="text-muted">Still not returned past due date</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number">12</div>
                            <div class="fw-bold">Active Students</div>
                            <small class="text-muted">Students currently inside the library</small>
                        </div>
                    </div>
                </div>

                <!-- REPORTS -->
                <h5 class="text-center mb-3">REPORTS AND TRENDS</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="reports-card">
                            <div class="fw-bold">TOTAL VISITORS TODAY</div>
                            <div class="stat-number">10</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="reports-card">
                            <div class="fw-bold">MOST BORROWED BOOK THIS WEEK</div>
                            <div class="stat-number">[BOOK TITLE]</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="reports-card">
                            <div class="fw-bold">PEAK TRAFFIC HOUR</div>
                            <div class="stat-number">HH:MM - HH:MM</div>
                        </div>
                    </div>
                </div>

                <!-- RECENT REPOSTR -->
                <h5 class="text-center mb-3">RECENT REPORTS</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead>
                            <tr>
                                <th>FILENAME</th>
                                <th>STATUS</th>
                                <th>TIME EXPORTED</th>
                                <th>EXPORTED BY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Filename</td>
                                <td>Status</td>
                                <td>Time Exported</td>
                                <td>Exported By</td>
                            </tr>
                            <tr>
                                <td>Filename</td>
                                <td>Status</td>
                                <td>Time Exported</td>
                                <td>Exported By</td>
                            </tr>
                            <tr>
                                <td>Filename</td>
                                <td>Status</td>
                                <td>Time Exported</td>
                                <td>Exported By</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-custom">VIEW FULL REPORT</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    fetch("sidebar.html")
        .then(response => response.text())
        .then(data => {
        document.getElementById("sidebar-container").innerHTML = data;

        // After sidebar loads, activate JS inside it
        const sidebar = document.querySelector(".sidebar");
        const toggleBtn = document.querySelector(".menu-toggle");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });

        // Highlight active link
        const links = document.querySelectorAll(".nav-links a");
        links.forEach(link => {
            if (window.location.href.includes(link.getAttribute("href"))) {
            link.classList.add("active");
            }
        });
        })
        .catch(err => console.error("Sidebar load error:", err));
    </script>

</body>
<script src="admin.js"></script>

</html>