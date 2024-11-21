var ctx = document.getElementById('ReclamationsChart').getContext('2d');
var canvas = document.getElementById('ReclamationsChart');
var LabelsR = JSON.parse(canvas.getAttribute('data-custom-labels'));
var DataR = JSON.parse(canvas.getAttribute('data-custom-data'));

var ReclamationsChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: LabelsR,
        datasets: [{
            label: 'Number of complaint',
            data: DataR,
            backgroundColor: [
              '#98BDFF',
              '#ff0000',
          ]
        }]
    },
    options: {
        cornerRadius: 5,
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
                    max: 560,
                    callback: function(value, index, values) {
                        return  value + '$' ;
                    },
                    autoSkip: true,
                    maxTicksLimit: 10,
                    fontColor:"#6C7383"
                }
            }],
            xAxes: [{
                stacked: false,
                ticks: {
                    beginAtZero: true,
                    fontColor: "#6C7383"
                },
                gridLines: {
                    color: "rgba(0, 0, 0, 0)",
                    display: false
                },
                barPercentage: 1
            }]
        },
        legend: {
            display: false
        },
        elements: {
            point: {
                radius: 0
            }
        }
    }
});
