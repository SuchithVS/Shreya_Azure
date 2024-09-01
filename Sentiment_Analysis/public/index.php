<?php
require_once "config.php";

// Fetch recent reviews for history
$sql = "SELECT * FROM reviews ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($link, $sql);
$recent_reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Analysis Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #f0f4f8; 
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
        }
        .container {
            max-width: 1200px;
        }
        .card { 
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .card:hover { 
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
        }
        .card-body {
            padding: 2rem;
        }
        .btn-custom {
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .history-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .history-card:hover {
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .sentiment-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .sentiment-positive { background-color: #48bb78; color: white; }
        .sentiment-negative { background-color: #f56565; color: white; }
        .sentiment-neutral { background-color: #ed8936; color: white; }
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 50px;
            height: 4px;
            background-color: #4299e1;
        }
        .delete-review {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .history-card:hover .delete-review {
            opacity: 1;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5 animate-fade-in" style="font-weight: 600;">Sentiment Analysis Dashboard</h1>
        
        <div class="row mb-5">
            <div class="col-md-4 mb-4 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-comment-dots fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Submit a Review</h5>
                        <p class="card-text">Share your thoughts and let our AI analyze the sentiment.</p>
                        <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#reviewModal">Submit Review</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-pie fa-3x mb-3 text-success"></i>
                        <h5 class="card-title">View Analysis</h5>
                        <p class="card-text">See the overall sentiment distribution of all reviews.</p>
                        <a href="analysis.php" class="btn btn-success btn-custom">View Analysis</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 animate-fade-in" style="animation-delay: 0.3s;">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-history fa-3x mb-3 text-info"></i>
                        <h5 class="card-title">Review History</h5>
                        <p class="card-text">Check your recent reviews and their sentiments.</p>
                        <a href="#history" class="btn btn-info btn-custom">View History</a>
                    </div>
                </div>
            </div>
        </div>

        <h2 id="history" class="section-title mb-4 animate-fade-in" style="animation-delay: 0.4s;">Recent Review History</h2>
        <?php foreach ($recent_reviews as $index => $review): ?>
            <div class="history-card animate-fade-in" style="animation-delay: <?php echo 0.5 + $index * 0.1; ?>s;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5><?php echo htmlspecialchars($review['customer_name']); ?></h5>
                    <span class="sentiment-badge sentiment-<?php echo $review['sentiment']; ?>">
                        <?php echo ucfirst($review['sentiment']); ?>
                    </span>
                </div>
                <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                <div class="text-end">
                    <button class="btn btn-sm btn-outline-danger delete-review" data-id="<?php echo $review['id']; ?>">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Submit a Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm">
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="customerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="reviewText" class="form-label">Your Review</label>
                            <textarea class="form-control" id="reviewText" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitReview">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Analysis Result Modal -->
    <div class="modal fade" id="analysisModal" tabindex="-1" aria-labelledby="analysisModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="analysisModalLabel">Sentiment Analysis Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="sentimentResult" class="mb-3"></div>
                    <div id="sentimentEmoji" style="font-size: 5rem;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#submitReview').click(function() {
                var customerName = $('#customerName').val();
                var reviewText = $('#reviewText').val();
                
                $.ajax({
                    url: 'submit_review.php',
                    method: 'POST',
                    data: { customer_name: customerName, review_text: reviewText },
                    success: function(response) {
                        $('#reviewModal').modal('hide');
                        var result = JSON.parse(response);
                        $('#sentimentResult').text('Sentiment: ' + result.sentiment);
                        $('#sentimentEmoji').text(result.sentiment === 'positive' ? '😃' : (result.sentiment === 'negative' ? '😞' : '😐'));
                        $('#analysisModal').modal('show');
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                });
            });

            $('.delete-review').click(function() {
                var reviewId = $(this).data('id');
                if (confirm('Are you sure you want to delete this review?')) {
                    $.ajax({
                        url: 'delete_review.php',
                        method: 'POST',
                        data: { id: reviewId },
                        success: function(response) {
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>