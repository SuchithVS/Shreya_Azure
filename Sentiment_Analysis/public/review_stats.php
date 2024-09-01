<?php
require_once "config.php";

$sql = "SELECT sentiment, COUNT(*) as count FROM reviews GROUP BY sentiment";
$result = mysqli_query($link, $sql);

$stats = [
    'positive' => 0,
    'negative' => 0,
    'neutral' => 0
];

while ($row = mysqli_fetch_assoc($result)) {
    $stats[$row['sentiment']] = $row['count'];
}

mysqli_free_result($result);
mysqli_close($link);

header('Content-Type: application/json');
echo json_encode($stats);
?>