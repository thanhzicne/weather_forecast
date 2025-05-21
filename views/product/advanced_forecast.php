<?php 
    session_start(); 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dự báo thời tiết</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/weather_forecast/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .search-bar {
            max-width: 400px;
            margin: 20px auto;
        }
        .weather-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }
        .weather-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .weather-card .card-title {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: white;
            padding: 10px;
            border-radius: 15px 15px 0 0;
            margin-bottom: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .weather-card .card-body {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
        }
        .forecast-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            overflow-x: auto;
            padding: 15px 0;
        }
        .forecast-day {
            flex: 0 0 auto;
            background: linear-gradient(135deg, #ffffff, #e6f0fa);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            min-width: 180px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, background 0.3s ease;
            border: 1px solid #e0e0e0;
        }
        .forecast-day:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #d3e3fd, #e6f0fa);
        }
        .forecast-day .date {
            font-weight: bold;
            font-size: 1rem;
            color: #333;
            margin-bottom: 10px;
        }
        .weather-icon {
            width: 60px; /* Adjust size */
            height: 60px;
            margin-bottom: 10px;
        }
        .temperature {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .temperature-high {
            color: #dc3545;
        }
        .temperature-low {
            color: #007bff;
        }
        .humidity-high {
            color: #17a2b8;
        }
        .forecast-day p {
            margin: 5px 0;
            font-size: 0.9rem;
            color: #555;
        }
        .forecast-day p.condition {
            font-weight: 500;
            color: #333;
        }
        .pagination .page-link {
            border-radius: 50%;
            margin: 0 5px;
            color: #007bff;
        }
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <?php
    require_once __DIR__ . '/../../controllers/WeatherPredictor.php';

    // Lấy từ khóa tìm kiếm
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Luôn dự báo 3 ngày
    $days_ahead = 3;

    $predictor = new WeatherPredictor();
    $predictions = $predictor->predict($days_ahead, $searchQuery);

    // Lấy thành phố cần hiển thị từ $predictions (nếu có dữ liệu)
    $cityToShow = !empty($predictions) ? array_key_first($predictions) : null;
    $sortedPredictions = !empty($cityToShow) ? [$cityToShow => $predictions[$cityToShow]] : [];

    // Phân trang (mặc dù chỉ có 1 thành phố, nhưng vẫn giữ logic phân trang)
    $itemsPerPage = 12;
    $totalItems = count($sortedPredictions);
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = isset($_GET['page']) ? max(1, min($totalPages, (int)$_GET['page'])) : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $paginatedPredictions = array_slice($sortedPredictions, $offset, $itemsPerPage, true);

    // lấy icon từ OpenWeatherMap icon codes
    $weatherIconMap = [
        'clear' => '01d',
        'clouds' => '03d',
        'rain' => '10d',
        'thunderstorm' => '11d',
        'snow' => '13d',
        'drizzle' => '09d',
        'mist' => '50d',
        'default' => '01d' // Fallback icon
    ];
    ?>
    <!-- Breadcrumb -->
    <div class="container mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="text-dark" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
            <span class="separator"> » </span>
            <li class="breadcrumb-item active" aria-current="page">Dự báo thời tiết demo</li>
        </ol>
    </div>
    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="text-center my-4 text-primary">🌦️ Dự báo thời tiết</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Ô tìm kiếm -->
        <div class="row mb-4 justify-content-center">
            <div class="col-md-5">
                <form method="GET" class="search-bar">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control shadow-sm rounded-pill border-0" placeholder="Tìm kiếm thành phố..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button class="btn btn-primary rounded-pill" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if (empty($sortedPredictions)): ?>
                <p class="text-center text-muted">Không có dữ liệu dự báo hoặc không tìm thấy thành phố.</p>
            <?php else: ?>
                <?php foreach ($paginatedPredictions as $city => $forecasts): ?>
                    <?php if (!is_array($forecasts)) continue; ?>
                    <div class="col-md-12">
                        <div class="weather-card">
                            <div class="card-title">
                                <?php echo htmlspecialchars($city); ?>
                            </div>
                            <div class="card-body">
                                <div class="forecast-row">
                                    <?php foreach ($forecasts as $forecast): ?>
                                        <?php if (!isset($forecast['temperature'], $forecast['humidity'], $forecast['condition'], $forecast['condition_vi'], $forecast['wind_speed'])) continue; ?>
                                        <div class="forecast-day">
                                            <div class="date"><?php echo htmlspecialchars($forecast['date']); ?></div>
                                            <?php
                                            // Get the OpenWeatherMap icon code
                                            $iconCode = $weatherIconMap[$forecast['condition']] ?? $weatherIconMap['default'];
                                            $iconUrl = "http://openweathermap.org/img/wn/{$iconCode}@2x.png";
                                            ?>
                                            <img src="<?php echo $iconUrl; ?>" alt="<?php echo htmlspecialchars($forecast['condition_vi']); ?>" class="weather-icon">

                                            <p class="temperature">
                                                <span class="<?php echo $forecast['temperature'] > 30 ? 'temperature-high' : ($forecast['temperature'] < 20 ? 'temperature-low' : ''); ?>">
                                                    <?php echo htmlspecialchars($forecast['temperature']); ?>°C
                                                </span>
                                            </p>
                                            <p class="condition"><?php echo htmlspecialchars($forecast['condition_vi']); ?></p>
                                            <p>
                                                <i class="fas fa-tint"></i> 
                                                <span class="<?php echo $forecast['humidity'] > 80 ? 'humidity-high' : ''; ?>">
                                                    <?php echo htmlspecialchars($forecast['humidity']); ?>%
                                                </span>
                                            </p>
                                            <p>
                                                <i class="fas fa-wind"></i> 
                                                <?php echo htmlspecialchars($forecast['wind_speed']); ?> m/s
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
    <button id="backToTop" class="btn btn-primary rounded-circle shadow">
        <i class="bi bi-chevron-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onscroll = function() {
            if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                document.getElementById("backToTop").style.display = "block";
            } else {
                document.getElementById("backToTop").style.display = "none";
            }
        };

        document.getElementById("backToTop").onclick = function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    </script>
</body>
</html>