<style>
    .forecast-btn.active {
        background-color: gold !important;
        color: black !important;
        border-color: gold !important;
    }
</style>

<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <main class="container mt-5">
        <!-- Breadcrumb -->
        <div class="container mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-dark" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
                <span class="separator"> » </span>
                <li class="breadcrumb-item active" aria-current="page">Dự báo thời tiết</li>
            </ol>
        </div>
        <!--  -->
        <section class="row d-flex justify-content-center text-center">
            <!-- Ô nhập tìm kiếm và chọn ngày -->
            <div class="col-md-4">
                <h2 class="fw-bold text-primary my-4">🌦️ Dự báo thời tiết</h2>
                <div class="input-group search-box">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
                    <input type="text" name="search-location" id="search-input" class="form-control shadow-sm rounded-pill border-0" placeholder="Tìm kiếm tỉnh, thành phố...">
                </div>
                <!-- Nút xem thời tiết các ngày tới  -->
                <div class="forecast-buttons mt-4 d-flex flex-column align-items-center">
                    <div class="d-flex justify-content-center mb-3 flex-wrap">
                        <button class="btn btn-primary forecast-btn mx-2 mb-2 px-4 py-2 rounded-pill shadow" data-day="0" id="todayBtn">Hôm nay</button>
                        <button class="btn btn-primary forecast-btn mx-2 mb-2 px-4 py-2 rounded-pill shadow" data-day="1">Ngày mai</button>
                        <button class="btn btn-primary forecast-btn mx-2 mb-2 px-4 py-2 rounded-pill shadow" data-day="2">Ngày kia</button>
                    </div>
                    <div class="d-flex justify-content-center flex-wrap">
                        <button class="btn btn-primary forecast-btn mx-2 mb-2 px-4 py-2 rounded-pill shadow" data-day="3">Ba ngày tới</button>
                        <button class="btn btn-primary forecast-btn mx-2 mb-2 px-4 py-2 rounded-pill shadow" data-day="4">Bốn ngày tới</button>
                    </div>
                </div>
                <!--  -->
            </div>
            
            <!-- Hiển thị thông tin thời tiết -->
            <div class="col-md-6">
                <div class="weather-box p-4 rounded shadow-lg weather-box ">
                    <section>
                        <div class="info-wrapper">
                            <h3 class="city-name">--</h3>
                            <p class="weather-state">--</p>
                            <img src="https://openweathermap.org/img/wn/10d@2x.png" alt="weather icon" class="weather-icon">
                            <h1 class="temperature display-4">--°C</h1>
                        </div>
                    </section>
                    <div class="border-top mt-3 pt-3"></div>
                    <div class="row">
                        <div class="col-6">
                            <p class="label">🌅 MT Mọc</p>
                            <p class="value sunrise">--</p>
                        </div>
                        <div class="col-6">
                            <p class="label">🌇 MT Lặn</p>
                            <p class="value sunset">--</p>
                        </div>
                    </div>
                    <div class="row border-top mt-2 pt-2">
                        <div class="col-6">
                            <p class="label">💧 Độ ẩm</p>
                            <p class="value"><span class="humidity">--</span></p>
                        </div>
                        <div class="col-6">
                            <p class="label">💨 Gió</p>
                            <p class="value"><span class="wind-speed">--</span></p>
                        </div>
                    </div>
                </div>
            </div>
                <!-- Bảng dự báo theo giờ -->
                <div class="air-quality border-top mt-4 pt-4">
                    <h4 class="text-left fw-bold text-primary mb-3"> Dự báo theo giờ tại <span class="aqi-city text-dark" >Hà Nội</span></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="hourly-forecast-table">
                            <thead class="table-primary ">
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Thời tiết</th>
                                    <th>Nhiệt độ</th>
                                    <th>Mô tả</th>
                                    <th>Độ ẩm</th>
                                    <th>Tốc độ gió</th>
                                </tr>
                            </thead>
                            <tbody >
                                <!-- Dữ liệu theo giờ sẽ được điền tại đây -->
                            </tbody>
                        </table>
                    </div>
                </div>
            <!--  -->
            <script src="/weather_forecast/assets/js/forecast.js"></script>
            <!-- hiệu ứng click button -->
            <script>
                const forecastButtons = document.querySelectorAll('.forecast-btn');
                const todayButton = document.getElementById('todayBtn');

                // Khi load trang: tự set nút Hôm nay active
                window.addEventListener('DOMContentLoaded', () => {
                    todayButton.classList.add('active');
                });

                // Khi click nút
                forecastButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        forecastButtons.forEach(btn => btn.classList.remove('active')); // Xóa active ở tất cả nút
                        button.classList.add('active'); // Active nút được click
                    });
                });
            </script>
    
        </section>
    </main>
    <?php include __DIR__ . '/../partials/footer.php'; ?>