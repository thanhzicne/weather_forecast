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
    <div class="container d-flex justify-content-between align-items-center">
        <a href="/weather_forecast/views/layouts/main.php" class="logo text-decoration-none d-flex align-items-center">
            <img src="/weather_forecast/assets/images/Logo/logo.png" alt="Logo" height="50">
            <span class="text-white fw-bold ms-2">Dubaothoitiet</span>
        </a>
        <div class="search-box position-relative">
            <input type="text" class="form-control search-input" id="searchCity" placeholder="Tìm kiếm thành phố...">
            <button class="search-icon-btn">
                <i class="bi bi-search search-icon"></i>
            </button>
        </div>

        <div class="time text-white">
            <i class="bi bi-clock"></i> Giờ địa phương: <span id="local-time"></span>
        </div>
    </div>
    <!-- Thanh tìm kiếm  -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const apiKey = "e2c222797835642c23ca9f8d6fda7d2b"; // OpenWeatherMap API Key
            const DEFAULT_CITY = "Hà Nội";
            const searchInput = document.getElementById("searchCity");
            const searchBtn = document.getElementById("searchBtn");
            let currentCity = DEFAULT_CITY;
            let currentLat = 21.0285;
            let currentLon = 105.8542;

            // Chuẩn hóa chuỗi
            function normalizeString(str) {
                return str
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/\s+/g, " ")
                    .trim();
            }

            // Kiểm tra tỉnh/thành phố hợp lệ
            async function checkCity(city) {
                city = normalizeString(city);
                const apiUrl = `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(city)},VN&limit=1&appid=${apiKey}`;
                try {
                    const response = await fetch(apiUrl);
                    if (!response.ok) return false;
                    const data = await response.json();
                    return data.length > 0 ? data[0] : false;
                } catch (error) {
                    console.error("Lỗi kiểm tra thành phố:", error);
                    return false;
                }
            }

            // Lưu dữ liệu thành phố vào localStorage
            function saveCityToStorage(cityData) {
                localStorage.setItem("selectedCity", JSON.stringify({
                    name: cityData.name || cityData,
                    lat: cityData.lat || currentLat,
                    lon: cityData.lon || currentLon
                }));
            }

            // Chuyển hướng đến forecast.php
            function redirectToForecast() {
                window.location.href = "/weather_forecast/views/product/forecast.php";
            }

            // Lấy tọa độ của tỉnh/thành phố
            async function fetchCityCoordinates(city) {
                const cityData = await checkCity(city);
                if (cityData) {
                    currentCity = cityData.name || city;
                    currentLat = cityData.lat;
                    currentLon = cityData.lon;
                    saveCityToStorage(cityData);
                    redirectToForecast();
                } else {
                    alert("Không tìm thấy tỉnh/thành phố. Vui lòng thử lại!");
                    searchInput.value = "";
                }
            }

            // Xử lý nhiều thành phố
            async function fetchWeatherByCities(cityList) {
                const cities = cityList.split(",").map(city => city.trim()).filter(city => city);
                if (cities.length > 1) {
                    alert("Vui lòng chỉ nhập một tỉnh/thành phố!");
                    return;
                }
                await fetchCityCoordinates(cities[0]);
            }

            // Xử lý tìm kiếm một thành phố
            async function fetchWeather(city) {
                if (!city) {
                    saveCityToStorage({ name: DEFAULT_CITY });
                    redirectToForecast();
                    return;
                }
                await fetchCityCoordinates(city);
            }

            // Xử lý tìm kiếm
            if (searchInput) {
                searchInput.addEventListener("keypress", function (e) {
                    if (e.key === "Enter") {
                        const cityList = searchInput.value.trim();
                        if (cityList) {
                            if (cityList.includes(",")) {
                                fetchWeatherByCities(cityList);
                            } else {
                                fetchWeather(cityList);
                            }
                        } else {
                            fetchWeather(DEFAULT_CITY);
                            currentCity = DEFAULT_CITY;
                            currentLat = 21.0285;
                            currentLon = 105.8542;
                            searchInput.value = "";
                        }
                    }
                });

                searchInput.addEventListener("input", (e) => {
                    const cityList = e.target.value.trim();
                    if (!cityList) {
                        fetchWeather(DEFAULT_CITY);
                        currentCity = DEFAULT_CITY;
                        currentLat = 21.0285;
                        currentLon = 105.8542;
                        searchInput.value = "";
                    }
                });

                searchInput.addEventListener("change", (e) => {
                    const cityList = e.target.value.trim();
                    if (cityList) {
                        if (cityList.includes(",")) {
                            fetchWeatherByCities(cityList);
                        } else {
                            fetchWeather(cityList);
                        }
                    } else {
                        fetchWeather(DEFAULT_CITY);
                        currentCity = DEFAULT_CITY;
                        currentLat = 21.0285;
                        currentLon = 105.8542;
                        searchInput.value = "";
                    }
                });

                // Sự kiện nhấn nút tìm kiếm
                if (searchBtn) {
                    searchBtn.addEventListener("click", function () {
                        const cityList = searchInput.value.trim();
                        if (cityList) {
                            if (cityList.includes(",")) {
                                fetchWeatherByCities(cityList);
                            } else {
                                fetchWeather(cityList);
                            }
                        } else {
                            fetchWeather(DEFAULT_CITY);
                            currentCity = DEFAULT_CITY;
                            currentLat = 21.0285;
                            currentLon = 105.8542;
                            searchInput.value = "";
                        }
                    });
                }
            }
        });
    </script>
    <!--  -->
</header>