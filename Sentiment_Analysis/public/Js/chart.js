$(document).ready(function() {
    $.getJSON('review_stats.php', function(data) {
        var ctx = document.getElementById('sentimentChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Positive', 'Negative', 'Neutral'],
                datasets: [{
                    data: [data.positive, data.negative, data.neutral],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Review Sentiment Distribution'
                }
            }
        });
    });
});