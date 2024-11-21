var ctx = document.getElementById('VAREChart').getContext('2d');
var canvas = document.getElementById('VAREChart');
var DataR1 = JSON.parse(canvas.getAttribute('data-custom-data-1'));
var DataR2 = JSON.parse(canvas.getAttribute('data-custom-data-2'));

var VAREChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Vehicles', 'Real Estates'], // Labels for the datasets
        datasets: [{
            label: 'Number of items',
            data: [DataR1, DataR2], // Use the variables directly
            backgroundColor: ['#98BDFF', '#ff0000'], // Background colors for each dataset
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 20,
                bottom: 0
            }
        },
        scales: {
            yAxes: [{
                display: true,
                gridLines: {
                    display: true,
                    drawBorder: false,
                    color: "#F2F2F2"
                },
                ticks: {
                    display: true,
                    min: 0,
                    callback: function(value, index, values) {
                        return  value + '$' ;
                    },
                    autoSkip: true,
                    maxTicksLimit: 10,
                    fontColor:"#6C7383"
                }
            }],
            xAxes: [{
                stacked: true,
                ticks: {
                    beginAtZero: true,
                    fontColor: "#6C7383"
                },
                gridLines: {
                    color: "rgba(0, 0, 0, 0)",
                    display: false
                },
            }]
        },
        legend: {
            display: true,
            labels: {
                fontColor: "#6C7383"
            }
        },
        elements: {
            point: {
                radius: 0
            }
        }
    }
});
