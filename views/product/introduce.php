<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <main class="container mt-4">
        <!-- Breadcrumb -->
        <div class="container mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-dark" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
                <span class="separator"> » </span>
                <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
            </ol>
        </div>
        <!--  -->
        <section class="intro p-5 bg-white shadow rounded">
            <h2 class="fw-bold text-center text-primary mb-4">🌤 Giới thiệu về Dubaothoitiet</h2>
            <p class="text-center">
                <strong>Dubaothoitiet</strong> là website cung cấp thông tin thời tiết chính xác, cập nhật theo thời gian thực, 
                giúp bạn theo dõi tình hình thời tiết tại các tỉnh/thành phố Việt Nam một cách dễ dàng.
            </p>

            <!-- Các tính năng chính -->
            <div class="row mt-5">
                <h3 class="text-secondary text-center mb-4">🔹 Tính năng nổi bật</h3>

                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                        <i class="bi bi-cloud-sun text-primary fs-1 me-3"></i>
                        <div>
                            <h5 class="fw-bold">Dự báo thời tiết</h5>
                            <p>Theo dõi dự báo thời tiết chi tiết theo giờ, ngày và tuần cho từng khu vực.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                        <i class="bi bi-bar-chart-line text-success fs-1 me-3"></i>
                        <div>
                            <h5 class="fw-bold">Biểu đồ trực quan</h5>
                            <p>Hiển thị biểu đồ nhiệt độ, lượng mưa theo thời gian để bạn dễ dàng theo dõi.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                        <i class="bi bi-megaphone text-danger fs-1 me-3"></i>
                        <div>
                            <h5 class="fw-bold">Cảnh báo thời tiết</h5>
                            <p>Thông báo về thời tiết cực đoan như mưa lớn, bão, sương mù và nắng nóng.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                        <i class="bi bi-clock text-warning fs-1 me-3"></i>
                        <div>
                            <h5 class="fw-bold">Giờ địa phương</h5>
                            <p>Hiển thị giờ địa phương của từng khu vực bạn tìm kiếm.</p>
                            <br>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Công nghệ sử dụng -->
            <div class="mt-5">
                <h3 class="text-secondary text-center mb-4">💡 Công nghệ sử dụng</h3>
                <div class="row text-center">
                    <div class="col-md-2 mb-4">
                        <i class="bi bi-bootstrap text-primary fs-1"></i>
                        <p class="mt-2 fw-bold">Bootstrap 5</p>
                    </div>
                    <div class="col-md-2 mb-4">
                        <i class="bi bi-cloud-arrow-down text-info fs-1"></i>
                        <p class="mt-2 fw-bold">API OpenWeatherMap</p>
                    </div>
                    <div class="col-md-2 mb-4">
                        <i class="bi bi-filetype-js text-warning fs-1"></i>
                        <p class="mt-2 fw-bold">JavaScript</p>
                    </div>
                    <div class="col-md-2 mb-4">
                        <i class="bi bi-filetype-html text-danger fs-1"></i>
                        <p class="mt-2 fw-bold">HTML & CSS</p>
                    </div>
                    <div class="col-md-2 mb-4">
                        <i class="bi bi-filetype-php text-secondary fs-1"></i>
                        <p class="mt-2 fw-bold">PHP</p>
                    </div>
                    <div class="col-md-2 mb-4">
                        <i class="bi bi-server text-success fs-1"></i>
                        <p class="mt-2 fw-bold">Laragon</p>
                    </div>
                </div>
            </div>
            <!--  -->
            <div class="text-center mt-5">
                <a href="../index.html" class="btn btn-primary btn-lg">🔍 Khám phá ngay</a>
            </div>
        </section>
    </main>
    <?php include __DIR__ . '/../partials/footer.php'; ?>