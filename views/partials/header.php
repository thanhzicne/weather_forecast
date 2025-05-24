<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dự báo thời tiết</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/weather_forecast/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<header class="header py-3 shadow-sm">
    <div class="container">
    <div class="header-inner row">
        <!-- Logo -->
        <div class="col-12 col-lg-4 d-flex align-items-center justify-content-center justify-content-lg-start">
            <a href="/weather_forecast/views/layouts/main.php" class="logo d-inline-block">
                <img src="/weather_forecast/assets/images/Logo/logo.png" alt="Dự báo thời tiết" height="50">
                <span class="text-white fw-bold ms-2">Dự báo thời tiết</span>
            </a>
        </div>

        <!-- Ô tìm kiếm -->
        <div class="col-12 col-lg-4 d-flex align-items-center justify-content-center">
            <div class="search">
                <div class="search-form search-location">
                    <form method="GET" action="/weather_forecast/views/product/advanced_forecast.php" id="searchForm">
                        <button type="submit" class="search-icon-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.3248 14.899L21.7048 20.279C21.8939 20.4682 22 20.7248 22 20.9924C21.9999 21.2599 21.8935 21.5164 21.7043 21.7055C21.515 21.8946 21.2584 22.0008 20.9909 22.0007C20.7234 22.0006 20.4669 21.8942 20.2778 21.705L14.8978 16.325C13.2895 17.5707 11.267 18.1569 9.24189 17.9644C7.21674 17.7718 5.341 16.815 3.99625 15.2886C2.6515 13.7622 1.93876 11.7808 2.00302 9.74755C2.06728 7.71428 2.90372 5.78186 4.34217 4.34341C5.78063 2.90495 7.71305 2.06851 9.74631 2.00425C11.7796 1.93999 13.761 2.65273 15.2874 3.99748C16.8138 5.34223 17.7706 7.21798 17.9631 9.24313C18.1556 11.2683 17.5694 13.2907 16.3238 14.899H16.3248ZM9.99977 16C11.5911 16 13.1172 15.3679 14.2424 14.2426C15.3676 13.1174 15.9998 11.5913 15.9998 10C15.9998 8.4087 15.3676 6.88258 14.2424 5.75736C13.1172 4.63214 11.5911 4 9.99977 4C8.40847 4 6.88235 4.63214 5.75713 5.75736C4.63191 6.88258 3.99977 8.4087 3.99977 10C3.99977 11.5913 4.63191 13.1174 5.75713 14.2426C6.88235 15.3679 8.40847 16 9.99977 16Z" fill="#9EA0A2"></path>
                            </svg>
                        </button>
                        <input type="text" name="search" class="search-input" id="searchCity" placeholder="Tìm kiếm thành phố...">
                        <div class="search-results">
                            <div class="loadingspinner"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Thời gian -->
        <div class="col-12 col-lg-4 d-flex align-items-center justify-content-center justify-content-lg-end">
            <div class="dropdown text-white">
                <button type="button" class="btn header-item time" id="time-local-btn" style="color: #fff;  border: 1px solid rgba(255, 255, 255, 0.1); padding: 5px 10px; border-radius: 8px; font-size: 14px; white-space: nowrap;">
                    <span><i class="bi bi-clock me-2" style="color: #fff;"></i>Giờ địa phương:</span>
                    <span id="local-time" class="ms-2 fs-5 fw-bold" style="color: #fff; width: 85px; display: inline-block; text-align: right; font-variant-numeric: tabular-nums;">22:03:00</span>
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Thanh tìm kiếm  -->   
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        // Xử lý sự kiện submit form
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            const searchQuery = document.getElementById('searchCity').value.trim();
            if (!searchQuery) {
                e.preventDefault();
                alert('Vui lòng nhập tên thành phố!');
                return false;
            }
            console.log('Form submitted with query:', searchQuery);
            // Form sẽ tự động chuyển hướng đến advanced_forecast.php với tham số search
        });

        // Cập nhật thời gian địa phương
        function updateLocalTime() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            document.getElementById('local-time').textContent = now.toLocaleTimeString('vi-VN', options);
        }
        updateLocalTime();
        setInterval(updateLocalTime, 1000);
        });
    </script>
    <!--  -->
</header>