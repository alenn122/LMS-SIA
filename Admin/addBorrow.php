<?php
include 'db.php';

$studentId = $_POST['studentId'];
$borrowDate = $_POST['borrowDate'];
$dueDate = $_POST['dueDate'];
$books = $_POST['books'] ?? [];

// Get Student_ID
$stmt = $conn->prepare("SELECT Student_ID FROM Students WHERE School_ID_Number=?");
$stmt->bind_param("s", $studentId);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();
$userId = $student['Student_ID'] ?? null;

if (!$userId) { 
    die("Student not found!"); 
}

foreach ($books as $bookId) {
    // Insert borrow record
    $sql = "INSERT INTO Borrow_Record (User_Type, User_ID, Book_ID, Borrow_Date, Due_Date) 
            VALUES ('student', ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $userId, $bookId, $borrowDate, $dueDate);
    $stmt->execute();

    // Update book copies: decrease available, increase borrowed
    $update = "UPDATE Book 
               SET Available_Copies = Available_Copies - 1,
                   Borrowed_Copies = Borrowed_Copies + 1
               WHERE Book_ID = ? AND Available_Copies > 0";
    $stmt2 = $conn->prepare($update);
    $stmt2->bind_param("i", $bookId);
    $stmt2->execute();
}

echo "Borrowing successfully recorded!";
