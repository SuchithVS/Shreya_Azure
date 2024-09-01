<?php
require_once "config.php";
require_once "ai_sentiment_analysis.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST["customer_name"]);
    $review_text = trim($_POST["review_text"]);
    
    $sentiment = analyze_sentiment($review_text);
    
    $sql = "INSERT INTO reviews (customer_name, review_text, sentiment) VALUES (?, ?, ?)";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $customer_name, $review_text, $sentiment);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true, "sentiment" => $sentiment]);
        } else {
            echo json_encode(["success" => false, "error" => mysqli_error($link)]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["success" => false, "error" => "Error preparing statement: " . mysqli_error($link)]);
    }
    
    mysqli_close($link);
}
?>