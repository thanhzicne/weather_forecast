<footer class="footer py-5 mt-5 bg-dark text-white">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6 border-end border-light pe-4">
                <div class="footer-info d-flex align-items-center">
                <a href="/weather_forecast/views/layouts/main.php" class="logo text-decoration-none d-flex align-items-center">
                    <img src="/weather_forecast/assets/images/Logo/logo.png" alt="Logo" height="50">
                </a>
                    <div>
                        <a href="/weather_forecast/views/layouts/main.php" style="text-decoration: none; color: white;">
                            <h5 class="fw-bold mb-3">Dubaothoitiet</h5>
                        </a>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Hồ Chí Minh</li>
                            <li class="mb-2"><i class="bi bi-envelope me-2"></i> Dubaothoitiet@email.com</li>
                            <li><i class="bi bi-telephone me-2"></i> 0123 456 789</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 pe-4 text-center">
                <h5 class="fw-bold mb-3">Tin tức</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a class="text-white text-decoration-none hover-link" href="/weather_forecast/views/layouts/main.php">Tổng hợp</a></li>
                    <li><a class="text-white text-decoration-none hover-link" href="/weather_forecast/views/product/news.php">Bản tin thời tiết</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 pe-4 text-center">
                <h5 class="fw-bold mb-3">Về chúng tôi</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a class="text-white text-decoration-none hover-link" href="/weather_forecast/views/product/introduce.php">Giới thiệu</a></li>
                    <li><a class="text-white text-decoration-none hover-link" href="/weather_forecast/views/product/author.php">Tác giả</a></li>
                </ul>
            </div>
            <!-- feedback -->
            <div class="col-lg-4 col-md-6 text-center">
                <h5 class="fw-bold mb-3">GÓP Ý</h5>
                <div class="w-100" style="max-width: 400px; margin: 0 auto;">
                    <form id="feedbackForm" method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="full_name" placeholder="Họ và tên" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="4" placeholder="Nội dung" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger px-4">Gửi góp ý</button>
                        </div>
                    </form>
                    <div id="feedbackMessage" style="margin-top: 10px; transition: opacity 0.3s ease;"></div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            // Sử dụng jQuery để gửi dữ liệu qua AJAX
            $('#feedbackForm').on('submit', function(e) {
                e.preventDefault(); // Ngăn form gửi theo cách thông thường

                // Kiểm tra dữ liệu đầu vào
                var fullName = $('input[name="full_name"]').val().trim();
                var email = $('input[name="email"]').val().trim();
                var content = $('textarea[name="content"]').val().trim();

                if (!fullName || !email || !content) {
                    $('#feedbackMessage').html('<div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin.</div>');
                    return;
                }

                // Kiểm tra định dạng email
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    $('#feedbackMessage').html('<div class="alert alert-danger">Email không hợp lệ.</div>');
                    return;
                }

                // Kiểm tra độ dài nội dung
                if (content.length < 10) {
                    $('#feedbackMessage').html('<div class="alert alert-danger">Nội dung phải dài ít nhất 10 ký tự.</div>');
                    return;
                }

                var formData = $(this).serialize(); // Lấy dữ liệu từ form

                // Hiển thị thông báo đang gửi
                $('#feedbackMessage').html('<div class="alert alert-info">Đang gửi...</div>').css('opacity', '0');
                setTimeout(() => $('#feedbackMessage').css('opacity', '1'), 100);

                $.ajax({
                    url: '/weather_forecast/feedback/submit',
                    type: 'POST',
                    data: formData,
                    dataType: 'json', // Yêu cầu jQuery tự động parse JSON
                    success: function(data) {
                        if (data.status === 'success') {
                            $('#feedbackMessage').html('<div class="alert alert-success">' + data.message + '</div>').css('opacity', '0');
                            $('#feedbackForm')[0].reset(); // Reset form
                        } else {
                            $('#feedbackMessage').html('<div class="alert alert-danger">' + data.message + '</div>').css('opacity', '0');
                        }
                        setTimeout(() => $('#feedbackMessage').css('opacity', '1'), 100);
                    },
                    error: function(xhr, status, error) {
                        $('#feedbackMessage').html('<div class="alert alert-danger">Lỗi AJAX: ' + status + ' - ' + error + ' (Status Code: ' + xhr.status + ')</div>').css('opacity', '0');
                        setTimeout(() => $('#feedbackMessage').css('opacity', '1'), 100);
                    }
                });
            });
            </script>
            <!--  -->
        </div>
        <hr class="border-light my-4">
        <!-- login admin -->
        <div class="text-center small">
            <a href="/weather_forecast/views/auth/login.php" style="text-decoration: none; color: inherit;">
                © 2025 Dự báo thời tiết
            </a>
</div>
    </div>
</footer>
<button id="backToTop" class="btn btn-primary rounded-circle shadow">
        <i class="bi bi-chevron-up"></i>
    </button>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/weather_forecast/assets/js/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/weather_forecast/assets/js/weather.js"></script>
    <script src="/weather_forecast/assets/js/dashboard.js"></script>
    <script src="/weather_forecast/assets/js/forecast.js"></script>
    <script src="/weather_forecast/assets/js/Specificallyforecast.js"></script>
</body>
</html>