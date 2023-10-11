<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .flex-center {
            align-items: center;
            display: flex;
            flex-direction: column;
            /* justify-content: center; */
        }

        .position-ref {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
        <div class="top-right links">
            @auth
            <a href="{{ url('/home') }}">Home</a>
            @else
            <a href="{{ route('login') }}">Login</a>

            @if (Route::has('register'))
            <a href="{{ route('register') }}">Register</a>
            @endif
            @endauth
        </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                折線圖
            </div>
            <div>
                <canvas id="myChart_1" width="600" height="600"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const ctx_1 = document.getElementById("myChart_1").getContext("2d");
                    const initialData = {
                        labels: ['2023-10-06 00:05:49', '2023-10-06 00:05:50', '2023-10-06 00:05:51', '2023-10-06 00:05:52', '2023-10-06 00:05:53', '2023-10-06 00:05:54', '2023-10-06 00:05:55', '2023-10-06 00:05:56', '2023-10-06 00:05:57', '2023-10-06 00:05:58'],
                        data: [10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
                    };
                    const myChart_1 = new Chart(ctx_1, {
                        type: "line",
                        data: {
                            labels: initialData.labels,
                            datasets: [{
                                    label: "圖表",
                                    data: initialData.data,
                                    fill: false,
                                    borderColor: 'rgb(54, 162, 235)', // 線的顏色
                                    backgroundColor: ['rgba(255, 99, 132, 0.5)'], // 點的顏色
                                    pointStyle: 'circle', //點類型為圓點
                                    pointRadius: 6, //圓點半徑
                                    pointHoverRadius: 10, //滑鼠動上去後圓點半徑
                                    tension: 0.1
                                }

                            ]
                        },
                        options: {
                            responsive: true, // 設置圖表為響應式

                            interaction: {
                                intersect: false,
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: '月份'
                                    }
                                },
                                y: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: '氣溫'
                                    }
                                }
                            }

                        }
                    });
                    let evtSource = new EventSource("/chartEventStream", {
                        withCredentials: true
                    });
                    evtSource.onmessage = function(e) {
                        let serverData = JSON.parse(e.data);
                        console.log('EventData:- ', serverData);


                        myChart.data.labels.push(serverData.time);
                        myChart.data.datasets[0].data.push(serverData.value);

                        // 移除舊的數據，保持一個固定的數據點數目
                        if (myChart.data.labels.length > 10) {
                            myChart.data.labels.shift();
                            myChart.data.datasets[0].data.shift();
                        }

                        myChart.update();

                    };
                </script>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.css"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [{
                    label: '圖表',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '時間軸'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '值'
                        }
                    }]
                }
            }
        });

        let evtSource = new EventSource("/chartEventStream", {
            withCredentials: true
        });
        evtSource.onmessage = function(e) {
            let serverData = JSON.parse(e.data);
            console.log('EventData:- ', serverData);

            myChart.data.labels.push(serverData.time);
            myChart.data.datasets[0].data.push(serverData.value);
            myChart.update();
        };
    </script>
</body>

</html>