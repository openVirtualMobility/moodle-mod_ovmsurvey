import Chart from 'chart.js';
import axios from 'axios';

let chartData, chartLabels = null;

axios.get('actions.php/chart_data')
    .then((res) => {
        var data = res.data.results;
        if (data.labels) {
            chartLabels = data.labels;
        }
        if (data.data) {
            chartData = data.data;
        }

        var config = {
            type: 'radar',
            plugins: [{
                beforeInit: function(chart) {
                    chart.data.labels.forEach(function(e, i, a) {
                        if (/\n/.test(e)) {
                            a[i] = e.split(/\n/);
                        }
                    });
                }
            }],
            options: {
                tooltips: {
                    backgroundColor: '#FFF',
                    titleFontSize: 15,
                    titleFontColor: '#686868',
                    bodyFontColor: '#686868',
                    bodyFontSize: 12,
                    displayColors: false,
                    borderColor: '#d1d1d1',
                    borderWidth: 1,
                    lineTension: 0.5,
                    pointBackgroundColor: "rgba(242, 119, 122, 1)",
                    pointBorderColor: "#fff",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(242, 119, 122, 1)"
                },
                scale: {
                    angleLines: {
                        display: true,
                        lineWidth: 0.5,
                        color: 'rgba(232, 230, 227, 0.85)'
                    },
                    gridLines: {
                        color: 'rgba(232, 230, 227, 0.85)',
                        display: true
                    },
                    pointLabels: {
                        callback: function(pointLabel, index, labels) {
                            return ' ';
                        } 
                    },
                    ticks: {
                        beginAtZero: true,
                        maxTicksLimit: 5,
                        min: 0,
                        max: 100,
                        display: true
                    }
                }
            },
            data: {
                labels: chartLabels,
                datasets: [{
                    label: ' ',
                    data: chartData,
                    backgroundColor: "rgba(186, 232, 255, 0.4)",
                    borderColor: "rgba(61, 179, 229, 1)",
                    fill: true,
                    radius: 6,
                    pointRadius: 2,
                    pointBorderWidth: 3,
                    pointBackgroundColor: "white",
                    pointBorderColor: "rgba(61, 179, 229, 1)",
                    pointHoverRadius: 10,
                }]
            }
        };

        var ctx = document.getElementById("report-chart").getContext("2d");
        window.myChart = new Chart(ctx, config);
    });