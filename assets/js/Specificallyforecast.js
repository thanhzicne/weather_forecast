const apiKey = "e2c222797835642c23ca9f8d6fda7d2b";
const cities = [
    "Hà Nội", "Hồ Chí Minh", "Hải Phòng", "Đà Nẵng", "Cần Thơ", "Thanh Hóa",
    "Nghệ An", "Huế", "Nha Trang", "Bình Dương", "Vũng Tàu",
    "Bắc Ninh", "Quảng Ngãi", "An Giang"
];
const DEFAULT_CITY = "Hà Nội";

// Hàm fetch weather dùng chung cho cả 2 trang
async function fetchWeather(city) {
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=vi&appid=${apiKey}`;

    try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error("Không tìm thấy thành phố");

        const data = await response.json();

        return {
            name: data.name,
            temp: Math.round(data.main.temp),
            tempMin: Math.round(data.main.temp_min),
            tempMax: Math.round(data.main.temp_max),
            humidity: data.main.humidity,
            windSpeed: (data.wind.speed * 3.6).toFixed(2), // m/s -> km/h
            description: data.weather[0].description,
            sunrise: new Date(data.sys.sunrise * 1000).toLocaleTimeString("vi-VN"),
            sunset: new Date(data.sys.sunset * 1000).toLocaleTimeString("vi-VN"),
            icon: `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`
        };
    } catch (error) {
        console.error(`Lỗi khi lấy dữ liệu cho ${city}:`, error);
        return null;
    }
}

// Cập nhật danh sách thẻ thời tiết
async function updateWeatherCards() {
    const container = document.getElementById("weather-cards");
    if (!container) return; // Không thực thi nếu không có container (trang chi tiết)

    container.innerHTML = "";

    for (const city of cities) {
        const weather = await fetchWeather(city);
        if (!weather) continue;

        const card = document.createElement("div");
        card.className = "col-lg-3 col-md-4 col-sm-6 col-12 p-2";

        card.innerHTML = `
            <div class="card weather-card shadow-sm border-primary text-center" data-city="${weather.name}" style="cursor:pointer;">
                <div class="card-body p-2">
                    <h6 class="card-title">${weather.name}</h6>
                    <img src="${weather.icon}" alt="weather icon">
                    <p class="weather-desc">${weather.description}</p>
                    <p class="temperature">${weather.tempMin}°C</p>
                    <p class="humidity">💧 ${weather.humidity}%</p>
                </div>
            </div>
        `;

        card.addEventListener("click", function () {
            localStorage.setItem("selectedCity", JSON.stringify(weather)); // Lưu thông tin thành phố vào localStorage
            window.location.href = "/weather_forecast/views/product/forecast.php"; // Chuyển hướng đến trang chi tiết
        });
        container.appendChild(card);
    }
}

// Cập nhật giao diện trang chi tiết
async function updateCityWeather(city) {
    const weather = await fetchWeather(city);
    if (!weather) {
        alert("Không tìm thấy thành phố, vui lòng thử lại.");
        return;
    }

    const cityNameEl = document.querySelector('.city-name');
    const descEl = document.querySelector('.weather-state');
    const iconEl = document.querySelector('.weather-icon');
    const tempEl = document.querySelector('.temperature');
    const sunriseEl = document.querySelector('.sunrise');
    const sunsetEl = document.querySelector('.sunset');
    const humidityEl = document.querySelector('.humidity');
    const windEl = document.querySelector('.wind-speed');

    if (cityNameEl) cityNameEl.innerHTML = weather.name;
    if (descEl) descEl.innerHTML = weather.description;
    if (iconEl) iconEl.setAttribute('src', weather.icon);
    if (tempEl) tempEl.innerHTML = `${weather.temp} °C`;
    if (sunriseEl) sunriseEl.innerHTML = weather.sunrise;
    if (sunsetEl) sunsetEl.innerHTML = weather.sunset;
    if (humidityEl) humidityEl.innerHTML = `${weather.humidity}%`;
    if (windEl) windEl.innerHTML = `${weather.windSpeed} km/h`;
}

// DOM ready
document.addEventListener("DOMContentLoaded", () => {
    updateWeatherCards(); // chỉ hoạt động nếu có container
    updateCityWeather(DEFAULT_CITY); // trang chi tiết
});

// Search input (chỉ hoạt động nếu có #search-input)
const searchInput = document.querySelector('#search-input');
if (searchInput) {
    searchInput.addEventListener('change', (e) => {
        const city = e.target.value.trim();
        if (city) {
            updateCityWeather(city);
        }
    });
}
