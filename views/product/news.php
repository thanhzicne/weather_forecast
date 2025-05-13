<style>
    /* ================================Nút phân trang========================= */
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 20px;
    }

    .page-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background-color: white;
        color: black;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .page-btn:hover {
        background-color: #e0e0e0;
    }

    .page-btn.active {
        background-color: #5c6eff;
        color: white;
    }

    .page-btn-arrow {
        font-size: 20px;
    }  
</style>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/navbar.php'; ?>
<main class="container mt-4 fade-in">
    <!-- Breadcrumb -->
    <div class="container mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="text-dark" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
            <span class="separator"> » </span>
            <li class="breadcrumb-item active" aria-current="page">Tin tức thời tiết</li>
        </ol>
    </div>
    <!--  -->
    <h2 class="news-section-title">Tổng hợp</h2>
    <div class="row">
        <!-- Nội dung chính -->
        <div class="col-md-8">
            <!-- Bài viết nổi bật -->
            <div class="news-featured-card mb-4">
                <img src="/weather_forecast/assets/images/News/New1/1.jpg" class="card-img-top" alt="Mưa axit">
                <div class="card-body">
                    <h5 class="card-title"><a href="/weather_forecast/views/news/new1.php" class="text-decoration-none text-dark">Mưa axit là gì? Mưa axit ảnh hưởng thế nào đến đời sống?</a></h5>
                    <p>Mưa axit là hiện tượng hóa học gây hại cho con người và môi trường sống. Mưa axit xảy ra khi các khí như SO2 và NOx phản ứng với nước trong không khí, tạo thành axit sulfuric và axit nitric...</p>
                </div>
            </div>
            <!-- Các bài viết nhỏ hơn -->
            <div id="news-list">
                <!-- 1 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New10/1.jpg" alt="Miền Bắc có mấy mùa">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new10.php" class="text-decoration-none">Biển Chết là gì? Vì sao Biển Chết lại có tên kỳ lạ như vậy?</a></h6>
                        <p>Biển Chết là gì? Biển Chết hay còn được biết đến là Tử Hải, nhưng nó thực chất không phải là biển...</p>
                    </div>
                </div>
                <!-- 2 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New9/1.jpg" alt="Biển Đen">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new9.php" class="text-decoration-none">Bão nhiệt đới là gì? Nguyên nhân hình thành và ảnh hưởng</a></h6>
                        <p>Bão nhiệt đới thường xuất hiện trên các vùng biển nhiệt đới có khả năng gây ra gió mạnh và mưa lớn. Đây là hiện tượng cực kỳ nguy hiểm và có thể gây thiệt hại về người và của...</p>
                    </div>
                </div>
                <!-- 3 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New8/1.jpg" alt="Biển Đỏ">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new8.php" class="text-decoration-none">Gió Tây ôn đới là gì? Đặc điểm và các vai trò của gió ôn đới</a></h6>
                        <p>Gió Tây ôn đới là loại gió thổi theo hướng Tây – Đông, có nguồn gốc từ khu vực áp cao cận nhiệt đới và di chuyển về phía áp thấp ôn đới. Gió này thổi quanh năm, mang theo nhiều hơi nước và thường gây mưa...</p>
                    </div>
                </div>
                <!-- 4 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New7/1.jpg" alt="Cao nguyên">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new7.php" class="text-decoration-none">Tại sao gọi là Biển Trắng? Biển Trắng thuộc đại dương nào?</a></h6>
                        <p>Biển Trắng là gì? Tại sao gọi là Biển Trắng? Đây là những câu hỏi phổ biến khi nhắc đến vùng biển độc đáo này...</p>
                    </div>
                </div>
                <!-- 5 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New6/1.jpg" alt="Gió mùa">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new6.php" class="text-decoration-none">Động đất là gì? Nguyên do hình thành và tác hại của động đất</a></h6>
                        <p>Động đất là gì? Đây là một hiện tượng tự nhiên thông thường hay còn chứa đựng sự tức giận của “mẹ thiên nhiên” đối với con người không...</p>
                    </div>
                </div>
                <!-- 6 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New1/1.jpg" alt="Sông băng">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new1.php" class="text-decoration-none">Mưa axit là gì? Mưa axit ảnh hưởng thế nào đến đời sống?</a></h6>
                        <p>Mưa axit là hiện tượng hóa học gây hại cho con người và môi trường sống...</p>
                    </div>
                </div>
                <!-- 7 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New2/1.jpg" alt="Sông băng">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new2.php" class="text-decoration-none">Biên độ nhiệt là gì? Công thức tính biên độ nhiệt chuẩn</a></h6>
                        <p>Biên độ nhiệt là gì? Biên độ nhiệt là sự thay đổi nhiệt độ được tính theo ngày, tháng hoặc năm...</p>
                    </div>
                </div>
                <!-- 8 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New3/1.jpg" alt="Sông băng">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new3.php" class="text-decoration-none">Miền Bắc có mấy mùa? Sự phân hóa khí hậu tại miền Bắc</a></h6>
                        <p>Miền Bắc có mấy mùa? Miền Bắc Việt Nam là một vùng đất có khí hậu phân bố theo 4 mùa rõ rệt, mỗi mùa mang đến những đặc trưng riêng biệt về thời tiết và cảnh quan...</p>
                    </div>
                </div>
                <!-- 9 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New4/1.jpg" alt="Sông băng">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new4.php" class="text-decoration-none">Biển Đen là gì? Vì sao Biển Đen có tên độc đáo như vậy?</a></h6>
                        <p>Biển Đen là gì?Biển Đen là một trong những vùng biển bí ẩn của thế giới...</p>
                    </div>
                </div>
                <!-- 10 -->
                <div class="news-article-item">
                    <img src="/weather_forecast/assets/images/News/New5/1.jpg" alt="Sông băng">
                    <div class="ms-3">
                        <h6><a href="/weather_forecast/views/news/new5.php" class="text-decoration-none">Biển Đỏ là gì? Biển Đỏ ở đâu? Vì sao lại có tên là Biển Đỏ?</a></h6>
                        <p>Biển Đỏ là gì? Biển Đỏ là một trong những vùng Biển Đỏ đáo có một không hai trên thế giới hiện nay...</p>
                    </div>
                </div>
                <!--  -->
            </div>
            <div id="custom-pagination" class="pagination-container mt-4 text-center"></div>
            <!--  -->
        </div>
        <!-- Thanh bên -->
        <aside class="col-md-4">

            <div class="news-sidebar-section">
                <h5 class="news-sidebar-title">Bài viết mới</h5>
                <ul class="news-sidebar-list">
                    <li>
                        <img src="/weather_forecast/assets/images/News/New2/1.jpg" class="card-img" alt="Mưa axit">
                        <a href="/weather_forecast/views/news/new2.php">Biên độ nhiệt là gì? Công thức tính biên độ nhiệt chuẩn</a>
                    </li>
                    <hr>
                    <li><a href="/weather_forecast/views/news/new1.php">Mưa axit là gì? Mưa axit ảnh hưởng thế nào đến đời sống?</a></li>
                    <hr>
                    <li><a href="/weather_forecast/views/news/new3.php">Miền Bắc có mấy mùa? Sự phân hóa khí hậu tại miền Bắc</a></li>
                    <hr>
                    <li><a href="/weather_forecast/views/news/new4.php">Biển Đen là gì? Vì sao Biển Đen có tên độc đáo?</a></li>
                    <hr>
                    <li><a href="/weather_forecast/views/news/new5.php">Biển Đỏ là gì? Vì sao lại có tên là Biển Đỏ?</a></li>
                </ul>
            </div>
            <!--  -->
            <div class="news-sidebar-section mt-4">
                <h5 class="news-sidebar-title">Video - Clip</h5>
                <div class="video-container">
                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/G1owGXEAEYY" frameborder="0" allowfullscreen></iframe>
                </div>
                <p class="text-center"><strong>Thiên tai nguy hiểm, lũ quét</strong></p>
            </div>
            <!--  -->
            <div class="news-sidebar-section mt-4">
                <h5 class="news-sidebar-title">Liên kết quốc tế</h5>
                <ul class="news-sidebar-list">
                    <li style="display: flex; align-items: center; justify-content: space-between;">
                        <img src="/weather_forecast/assets/images/LogoNews/logo2.svg" alt="logo" style="width: 50px; height: auto; object-fit: cover; border-radius: 5px;">
                        <a href="https://wmo.int/" style="flex: 1; margin-left: 10px; text-decoration: none; color: #333;">Tổ chức khí tượng thế giới</a>
                    </li>
                    <hr>
                    <li style="display: flex; align-items: center; justify-content: space-between;">
                        <img src="/weather_forecast/assets/images/LogoNews/logo4.png" alt="logo" style="width: 50px; height: auto; object-fit: cover; border-radius: 5px;">
                        <a href="https://openweathermap.org/city/1583992" style="flex: 1; margin-left: 10px; text-decoration: none; color: #333;">OpenWeatherMap</a>
                    </li>
                </ul>
            </div>
            <!--  -->
            <div class="news-sidebar-section mt-4">
                <h5 class="news-sidebar-title">Liên kết trong nước</h5>
                <ul class="news-sidebar-list">
                    <li style="display: flex; align-items: center; justify-content: space-between;">
                        <img src="/weather_forecast/assets/images/LogoNews/logo5.png" alt="logo" style="width: 50px; height: auto; object-fit: cover; border-radius: 5px;">
                        <a href="https://www.monre.gov.vn/" style="flex: 1; margin-left: 10px; text-decoration: none; color: #333;">Bộ Nông nghiệp và Môi trường</a>
                    </li>
                    <hr>
                    <li style="display: flex; align-items: center; justify-content: space-between;">
                        <img src="/weather_forecast/assets/images/LogoNews/logo6.png" alt="logo" style="width: 50px; height: auto; object-fit: cover; border-radius: 5px;">
                        <a href="https://imh.ac.vn/" style="flex: 1; margin-left: 10px; text-decoration: none; color: #333;">Viện khoa học KTTV và Biến đổi Khí hậu</a>
                    </li>
                    <hr>
                    <li style="display: flex; align-items: center; justify-content: space-between;">
                        <img src="/weather_forecast/assets/images/LogoNews/logo7.png" alt="logo" style="width: 50px; height: auto; object-fit: cover; border-radius: 5px;">
                        <a href="https://nchmf.gov.vn/Kttv/vi-VN/1/index.html" style="flex: 1; margin-left: 10px; text-decoration: none; color: #333;">Cục khí tượng thuỷ văn trung tâm dự báo khí tượng quốc gia</a>
                    </li>
                    
                </ul>
            </div>        
            <!--  -->
        </aside>
    </div>
    <!-- js phân trang -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const itemsPerPage = 8;
            const articles = Array.from(document.querySelectorAll("#news-list .news-article-item"));
            const totalPages = Math.ceil(articles.length / itemsPerPage);

            let currentPage = 1;

            function showPage(page) {
            currentPage = page;
            articles.forEach((item, index) => {
                item.style.display = (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) ? "flex" : "none";
            });

            renderPagination();
            }

            function renderPagination() {
        const pagination = document.getElementById("custom-pagination");
        pagination.innerHTML = "";

        // Nút quay lại <
        if (currentPage > 1) {
            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = "&#8249;"; // mũi tên <
            prevBtn.className = "page-btn page-btn-arrow";
            prevBtn.addEventListener("click", () => showPage(currentPage - 1));
            pagination.appendChild(prevBtn);
        }

        // Các nút số trang
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.className = "page-btn" + (i === currentPage ? " active" : "");
            btn.addEventListener("click", () => showPage(i));
            pagination.appendChild(btn);
        }

        // Nút tiếp theo >
        if (currentPage < totalPages) {
            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = "&#8250;"; // mũi tên >
            nextBtn.className = "page-btn page-btn-arrow";
            nextBtn.addEventListener("click", () => showPage(currentPage + 1));
            pagination.appendChild(nextBtn);
            }
        }
            showPage(currentPage);
        });
    </script>
    <!--  -->
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>