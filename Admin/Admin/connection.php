<?php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "lms_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $password, $db_name);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
