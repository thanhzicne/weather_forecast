<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <main class="container mt-4">
        <!-- Breadcrumb -->
        <div class="container mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-dark" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
                <span class="separator">&nbsp;»&nbsp; </span>
                <li class="breadcrumb-item active" aria-current="page">Biểu đồ nhiệt độ, lượng mưa</li>
            </ol>
        </div>
        <!--  -->
        <h2 class="text-center">Biểu đồ nhiệt độ, lượng mưa</h2>
        <!-- Ô nhập tìm kiếm tỉnh/thành phố -->
        <section class="mt-5">
            <div class="input-group mb-3">
                <input type="text" id="cityInput" class="form-control" placeholder="Nhập tỉnh/thành phố..." autocomplete="off">
                <button id="searchButton" class="btn btn-light">
                    <i class="fa fa-search"></i>
                </button>
            </div>
            <div class="chart-container d-flex justify-content-center align-items-center p-3" style="height: 400px;">
                <canvas id="weatherChart"></canvas>
            </div>
        </section>
    </main>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
    <!--  -->
    <script src="/weather_forecast/assets/js/weather.js"></script>
    <script src="/weather_forecast/assets/js/Specificallyforecast.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>