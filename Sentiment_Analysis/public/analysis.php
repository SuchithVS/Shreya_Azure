<?php
require_once "config.php";

$sql = "SELECT sentiment, COUNT(*) as count FROM reviews GROUP BY sentiment";
$result = mysqli_query($link, $sql);

$sentiments = array('positive' => 0, 'negative' => 0, 'neutral' => 0);
while ($row = mysqli_fetch_assoc($result)) {
    $sentiments[$row['sentiment']] = $row['count'];
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Analysis Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://d3js.org/d3.v5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3-cloud/1.2.5/d3.layout.cloud.min.js"></script>
    <style>
        body { 
            background-color: #f0f4f8; 
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
        }
        .container {
            max-width: 800px;
        }
        .chart-container {
            background-color: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .summary-card {
            transition: all 0.3s ease;
        }
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5 animate-fade-in" style="font-weight: 600;">Sentiment Analysis Results</h1>
        
        <div class="chart-container animate-fade-in" style="animation-delay: 0.2s;">
            <canvas id="sentimentChart"></canvas>
        </div>
        
        <div class="row mt-4 animate-fade-in" style="animation-delay: 0.4s;">
            <div class="col-md-4 mb-3">
                <div class="card text-center summary-card">
                    <div class="card-body">
                        <h5 class="card-title">Positive</h5>
                        <p class="card-text" style="font-size: 2rem; color: #48bb78;">
                            <?php echo round(($sentiments['positive'] / array_sum($sentiments)) * 100, 1); ?>%
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center summary-card">
                    <div class="card-body">
                        <h5 class="card-title">Neutral</h5>
                        <p class="card-text" style="font-size: 2rem; color: #ed8936;">
                            <?php echo round(($sentiments['neutral'] / array_sum($sentiments)) * 100, 1); ?>%
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center summary-card">
                    <div class="card-body">
                        <h5 class="card-title">Negative</h5>
                        <p class="card-text" style="font-size: 2rem; color: #f56565;">
                            <?php echo round(($sentiments['negative'] / array_sum($sentiments)) * 100, 1); ?>%
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="chart-container animate-fade-in" style="animation-delay: 0.6s;">
            <h2 class="text-center mb-4">Most Common Words in Reviews</h2>
            <div id="word-cloud" style="width: 100%; height: 400px;"></div>
        </div>
        
        <div class="text-center mt-5 animate-fade-in" style="animation-delay: 0.8s;">
            <a href="index.php" class="btn btn-primary btn-custom">Back to Home</a>
        </div>
    </div>

    <script>
        // Doughnut Chart
        var ctx = document.getElementById('sentimentChart').getContext('2d');
        var sentimentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Negative', 'Neutral'],
                datasets: [{
                    data: [<?php echo $sentiments['positive'] . ',' . $sentiments['negative'] . ',' . $sentiments['neutral']; ?>],
                    backgroundColor: ['#48bb78', '#f56565', '#ed8936'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Sentiment Distribution',
                        font: {
                            size: 20,
                            weight: 'bold',
                            family: "'Poppins', sans-serif"
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = Math.round((value / total) * 100);
                                return label + ': ' + percentage + '%';
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        // Word Cloud
        fetch('word_cloud_data.php')
            .then(response => response.json())
            .then(data => {
                var words = Object.keys(data).map(function(key) {
                    return {text: key, size: 10 + data[key] * 5};
                });

                var layout = d3.layout.cloud()
                    .size([800, 400])
                    .words(words)
                    .padding(5)
                    .rotate(function() { return ~~(Math.random() * 2) * 90; })
                    .font("'Poppins', sans-serif")
                    .fontSize(function(d) { return d.size; })
                    .on("end", draw);

                layout.start();

                function draw(words) {
                    d3.select("#word-cloud").append("svg")
                        .attr("width", layout.size()[0])
                        .attr("height", layout.size()[1])
                        .append("g")
                        .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
                        .selectAll("text")
                        .data(words)
                        .enter().append("text")
                        .style("font-size", function(d) { return d.size + "px"; })
                        .style("font-family", "'Poppins', sans-serif")
                        .style("fill", function(d, i) { return d3.schemeCategory10[i % 10]; })
                        .attr("text-anchor", "middle")
                        .attr("transform", function(d) {
                            return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                        })
                        .text(function(d) { return d.text; });
                }
            });
    </script>
</body>
</html>