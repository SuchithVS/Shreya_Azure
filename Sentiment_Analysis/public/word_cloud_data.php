<?php
require_once "config.php";

$sql = "SELECT review_text FROM reviews";
$result = mysqli_query($link, $sql);

$word_count = array();
$stop_words = array('the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by');

while ($row = mysqli_fetch_assoc($result)) {
    $words = str_word_count(strtolower($row['review_text']), 1);
    foreach ($words as $word) {
        if (!in_array($word, $stop_words) && strlen($word) > 2) {
            if (isset($word_count[$word])) {
                $word_count[$word]++;
            } else {
                $word_count[$word] = 1;
            }
        }
    }
}

arsort($word_count);
$word_count = array_slice($word_count, 0, 50);

echo json_encode($word_count);

mysqli_close($link);
?>