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
        .weather-icon {
            font-size: 2rem;
            margin-right: 10px;
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
        .search-bar {
            max-width: 400px;
            margin: 20px auto;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php 
    session_start(); 
    ?>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <?php
    require_once __DIR__ . '/../../controllers/WeatherPredictor.php';

    $predictor = new WeatherPredictor();
    $predictions = $predictor->predict();
    $date = date('Y-m-d', strtotime('+1 day'));

    // Phân trang
    $itemsPerPage = 12;
    $totalItems = count($predictions);
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = isset($_GET['page']) ? max(1, min($totalPages, (int)$_GET['page'])) : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Tìm kiếm
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filteredPredictions = array_filter($predictions, function($city) use ($searchQuery) {
        if (!is_string($city)) {
            return false;
        }
        return empty($searchQuery) || stripos($city, $searchQuery) !== false;
    }, ARRAY_FILTER_USE_KEY);

    $totalFilteredItems = count($filteredPredictions);
    $totalFilteredPages = ceil($totalFilteredItems / $itemsPerPage);
    $currentPage = $totalFilteredPages > 0 ? min($currentPage, $totalFilteredPages) : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $paginatedPredictions = array_slice($filteredPredictions, $offset, $itemsPerPage);
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
        <h1 class="text-center my-4">Dự báo thời tiết nâng cao (Ngày mai: <?php echo htmlspecialchars($date); ?>)</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Ô tìm kiếm -->
        <form class="search-bar" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm thành phố..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <div class="row">
            <?php if (empty($filteredPredictions)): ?>
                <p class="text-center">Không có dữ liệu dự báo cho ngày mai hoặc không tìm thấy thành phố.</p>
            <?php else: ?>
                <?php foreach ($paginatedPredictions as $city => $forecast): ?>
                    <?php
                    if (!is_array($forecast) || !isset($forecast['temperature'], $forecast['humidity'], $forecast['condition'], $forecast['condition_vi'], $forecast['wind_speed'])) {
                        continue;
                    }
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php
                                    $conditionIcon = match ($forecast['condition']) {
                                        'clear' => 'fas fa-sun',
                                        'clouds' => 'fas fa-cloud',
                                        'rain' => 'fas fa-cloud-rain',
                                        'thunderstorm' => 'fas fa-bolt',
                                        'snow' => 'fas fa-snowflake',
                                        'mist' => 'fas fa-smog',
                                        default => 'fas fa-question',
                                    };
                                    ?>
                                    <i class="<?php echo $conditionIcon; ?> weather-icon"></i>
                                    <?php echo htmlspecialchars($city); ?>
                                </h5>
                                <p class="card-text">
                                    Nhiệt độ: 
                                    <span class="<?php echo $forecast['temperature'] > 30 ? 'temperature-high' : ($forecast['temperature'] < 20 ? 'temperature-low' : ''); ?>">
                                        <?php echo htmlspecialchars($forecast['temperature']); ?>°C
                                    </span>
                                </p>
                                <p class="card-text">
                                    Độ ẩm: 
                                    <span class="<?php echo $forecast['humidity'] > 80 ? 'humidity-high' : ''; ?>">
                                        <?php echo htmlspecialchars($forecast['humidity']); ?>%
                                    </span>
                                </p>
                                <p class="card-text">Tình trạng: <?php echo htmlspecialchars($forecast['condition_vi']); ?></p>
                                <p class="card-text">Tốc độ gió: <?php echo htmlspecialchars($forecast['wind_speed']); ?> m/s</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Phân trang -->
        <?php if ($totalFilteredPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($searchQuery); ?>" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalFilteredPages; $i++): ?>
                        <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $currentPage == $totalFilteredPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($searchQuery); ?>" aria-label="Next">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
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