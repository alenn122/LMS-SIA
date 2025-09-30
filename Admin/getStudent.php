<?php
include 'db.php';

if (!isset($_GET['q'])) exit;

$search = mysqli_real_escape_string($conn, $_GET['q']);

$sql = "SELECT * FROM Students WHERE School_ID_Number LIKE '%$search%' LIMIT 5";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $picture = !empty($row['Picture']) ? $row['Picture'] : '/img/default-profile.png';
        echo '<button type="button" class="list-group-item list-group-item-action suggest-item" 
                data-id="'.htmlspecialchars($row['School_ID_Number']).'" 
                data-name="'.htmlspecialchars($row['Name']).'" 
                data-program="'.htmlspecialchars($row['Course']).'" 
                data-year="'.htmlspecialchars($row['Year_Level']).'" 
                data-pic="'.htmlspecialchars($picture).'">';
        echo '<img src="'.htmlspecialchars($picture).'" style="width:40px;height:40px;object-fit:cover;border-radius:50%;margin-right:10px;">';
        echo htmlspecialchars($row['School_ID_Number']) . ' - ' . htmlspecialchars($row['Name']);
        echo '</button>';
    }
} else {
    echo '<div class="list-group-item text-muted">No student found</div>';
}
?>
