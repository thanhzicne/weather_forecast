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
            background: linear-gradient(135deg, rgb(247, 247, 247));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .container {
            max-width: 1200px;
        }

        .search-bar {
            max-width: 500px;
            margin: 20px auto;
        }

        .weather-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .weather-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .weather-card .card-title {
            background: linear-gradient(90deg, #2196f3, #00bcd4);
            color: white;
            padding: 14px;
            font-size: 1.6rem;
            font-weight: 600;
            text-align: center;
        }

        .weather-card .card-body {
            padding: 25px;
            background: #f9f9f9;
        }

        .forecast-row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px; /* Giảm gap để phù hợp với 7 ngày */
        }

        .forecast-day {
            flex: 0 0 auto;
            background: linear-gradient(135deg, #b3e5fc, #e1f5fe);
            border-radius: 12px;
            padding: 12px; /* Giảm padding */
            text-align: center;
            min-width: 120px; /* Giảm min-width để hiển thị 7 ngày tốt hơn */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, background 0.3s ease;
            border: 1px solid #90caf9;
        }

        .forecast-day:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #81d4fa, #b2ebf2);
        }

        .forecast-day .date {
            font-weight: 600;
            font-size: 0.85rem; /* Giảm font-size */
            margin-bottom: 8px;
            color: #212121;
        }

        .weather-icon {
            width: 40px; /* Giảm kích thước icon */
            height: 40px;
            margin-bottom: 8px;
        }

        .temperature {
            font-size: 1.3rem; /* Giảm font-size */
            font-weight: bold;
            margin-bottom: 8px;
        }

        .temperature-high {
            color: #e53935;
        }

        .temperature-low {
            color: #1e88e5;
        }

        .humidity-high {
            color: #00838f;
        }

        .forecast-day p {
            margin: 3px 0;
            font-size: 0.8rem; /* Giảm font-size */
            color: #555;
        }

        .forecast-day p.condition {
            font-weight: 500;
            color: #333;
        }

        .breadcrumb {
            background: transparent;
            font-size: 0.95rem;
        }

        .breadcrumb .breadcrumb-item a {
            text-decoration: none;
            color: #0277bd;
        }

        .breadcrumb .breadcrumb-item.active {
            color: #333;
        }

        #backToTop {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            display: none;
            width: 48px;
            height: 48px;
            font-size: 20px;
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

    // Luôn dự báo 7 ngày
    // Số ngày muốn dự đoán
    $days_ahead = 7;

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

    // Lấy icon từ OpenWeatherMap icon codes
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
            <li class="breadcrumb-item"><a class="text-dark text-decoration-underline" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
            <span class="separator">  » </span>
            <li class="breadcrumb-item active" aria-current="page">Dự báo thời tiết</li>
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
        <!--  -->
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