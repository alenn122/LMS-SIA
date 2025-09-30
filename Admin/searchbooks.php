<?php
include 'db.php';

$q = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

if (!empty($q)) {
    $sql = "SELECT * FROM Book WHERE Title LIKE '%$q%' OR Author LIKE '%$q%' LIMIT 10";
} else {
    $sql = "SELECT * FROM Book LIMIT 10";
}

$result = mysqli_query($conn, $sql);
$books = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($book = mysqli_fetch_assoc($result)) {
        $books[] = [
            'Book_ID' => $book['Book_ID'],
            'Title' => $book['Title'],
            'Author' => $book['Author'],
            'Category' => $book['Category']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($books);
exit;
?>