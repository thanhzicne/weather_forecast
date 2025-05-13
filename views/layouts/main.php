
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <main class="container mt-4">
        <section class="row text-center">
            <!--  -->
            <div class="col-md-6">
                <div class="weather-box p-4 rounded shadow-lg">
                    <section>
                        <div class="info-wrapper text-center">
                            <h3 class="city-name">Hà Nội</h3>
                                <p class="weather-state">--</p>
                                <img src="" alt="weather icon" class="weather-icon">
                                <h1 class="temperature display-4">--°C</h1>
                    </div>
                    </section>
                </div>
            </div>

            <!--  -->
            <div class="col-md-6 p-0">
                <div class="shadow-lg weather-box p-0 m-0" style="width: 100%; height: 100%;">
                    <iframe 
                        style="width: 100%; height: 100%; border: 0; border-radius: 10px; overflow: hidden;" 
                        loading="lazy" 
                        allowfullscreen 
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.8569335854493!2d105.8342!3d21.0278!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f100!3m3!1m2!1s0x3135ab0000000000%3A0x0000000000000000!2zSMOgIE7hur1pLCBWaeG7h3QgbmFt!5e0!3m2!1svi!2s!4v1710930480013">
                    </iframe>
                </div>
            </div>
        </section>      
        <!-- Tin tức thời tiết -->
        <section class="row mt-4">
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light">
                    <h3 class="text-center">Tin tức nổi bật</h3>
                    <div id="news-list">
                <!-- 1 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New10/1.jpg" alt="Miền Bắc có mấy mùa" style="width:75px; height:auto;">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new10.php" class="text-decoration-none text-dark" style="font-size: 12px;">Biển Chết là gì? Vì sao Biển Chết lại có tên kỳ lạ như vậy?</a></h6>
                    </div>
                </div>
                <!-- 10 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New5/1.jpg" alt="Sông băng" style="width:75px; height:auto;">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new5.php" class="text-decoration-none text-dark" style="font-size: 12px;">Biển Đỏ là gì? Biển Đỏ ở đâu? Vì sao lại có tên là Biển Đỏ?</a></h6>
                    </div>
                </div>
                <!-- 3 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New8/1.jpg" alt="Biển Đỏ" style="width:75px; height:auto;" >
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new8.php" class="text-decoration-none text-dark" style="font-size: 12px;">Gió Tây ôn đới là gì? Đặc điểm và các vai trò của gió ôn đới</a></h6>
                    </div>
                </div>
                <!--  -->
            </div>
            <div id="custom-pagination" class="pagination-container mt-4 text-center"></div>
            <!--  -->
        </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light">
                    <h3 class="text-center">Tin tức tổng hợp</h3>
                    <div id="news-list">
                <!-- 6 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New1/1.jpg" alt="Sông băng" style="width:75px; height:auto;" >
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new1.php" class="text-decoration-none text-dark" style="font-size: 12px;">Mưa axit là gì? Mưa axit ảnh hưởng thế nào đến đời sống?</a></h6>
                    </div>
                </div>
                <!-- 7 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New2/1.jpg" alt="Sông băng" style="width:75px; height:auto;" >
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new2.php" class="text-decoration-none text-dark" style="font-size: 12px;">Biên độ nhiệt là gì? Công thức tính biên độ nhiệt chuẩn</a></h6>
                    </div>
                </div>
                <!-- 9 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New4/1.jpg" alt="Sông băng" style="width:75px; height:auto;">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new4.php" class="text-decoration-none text-dark"  style="font-size: 12px;">Biển Đen là gì? Vì sao Biển Đen có tên độc đáo như vậy?</a></h6>
                    </div>
                </div>
                <!--  -->
            </div>
            <div id="custom-pagination" class="pagination-container mt-4 text-center"></div>
            <!--  -->
        </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light">
                    <h3 class="text-center">Lịch dương</h3>
                    <div class="d-flex justify-content-center">
                        <div id="calendar"></div>
                    </div>
                    <script src="./js/date.js"></script>
                </div>
            </div>
        </section>
        <!-- Thời tiết các tỉnh nổi bật -->
        
        <div class="container-fluid mt-4">
            <h2 class="text-center my-4">Thời tiết các tỉnh nổi bật</h2> 
            <div class="row g-2" id="weather-cards"></div>
        </div>

        <script src="./js/Specificallyforecast.js"></script>
        <!-- Biểu đồ -->
        <section class="mt-5">
            <h2 class="text-center">Biểu đồ nhiệt độ, lượng mưa</h2>
            <!-- Ô nhập tìm kiếm tỉnh/thành phố -->
            <section class="mt-5">
                <div class="input-group mb-3">
                    <input type="text" id="cityInput" class="form-control" placeholder="Nhập tỉnh/thành phố..." autocomplete="off">
                    <button id="searchButton" class="btn btn-light">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <div class="chart-container d-flex justify-content-center align-items-center p-3">
                    <canvas id="weatherChart" style="height: 350px; width: 700px;"></canvas>
                </div>
                </section>
        </section>
        
    </main>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
    <!--  -->
    <script src="/weather_forecast/assets/js/weather.js"></script>
    <script src="/weather_forecast/assets/js/Specificallyforecast.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>