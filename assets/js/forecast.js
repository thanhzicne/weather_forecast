document.addEventListener("DOMContentLoaded", function () {
    const apiKey = "e2c222797835642c23ca9f8d6fda7d2b"; // OpenWeatherMap API Key
    const DEFAULT_CITY = "Hà Nội"; // Mặc định là Hà Nội
    const searchInput = document.getElementById("search-input");
    const forecastButtons = document.querySelectorAll(".forecast-btn");
    const weatherContainer = document.querySelector(".weather-box");
    const hourlyTableBody = document.querySelector("#hourly-forecast-table tbody");
    const cityNameInHourlyTable = document.querySelector(".aqi-city");

    // Biến lưu thông tin tỉnh/thành phố và tọa độ
    let currentCity = "Hà Nội"; // mặc định
    const storedCityData = localStorage.getItem("selectedCity");
    if (storedCityData) {
        try {
            const cityData = JSON.parse(storedCityData);
            if (cityData && cityData.name) {
                currentCity = cityData.name;
            }
        } catch (e) {
            console.warn("Lỗi khi phân tích dữ liệu thành phố từ localStorage:", e);
        }
    }



    // Bảng dịch mô tả thời tiết sang tiếng Việt
    const weatherDescriptions = {
        "clear sky": "Trời quang",
        "few clouds": "Ít mây",
        "scattered clouds": "Mây rải rác",
        "broken clouds": "Mây đứt đoạn",
        "overcast clouds": "Nhiều mây",
        "light rain": "Mưa nhẹ",
        "moderate rain": "Mưa vừa",
        "heavy rain": "Mưa to",
        "shower rain": "Mưa rào",
        "thunderstorm": "Dông",
        "snow": "Tuyết rơi",
        "mist": "Sương mù",
        "fog": "Sương mù dày",
        "haze": "Mù khô"
    };

    // Chuẩn hóa chuỗi (loại bỏ dấu, khoảng trắng thừa)
    function normalizeString(str) {
        return str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/\s+/g, " ")
            .trim();
    }

    // Lấy tọa độ của tỉnh/thành phố từ OpenWeather API
    async function getCityCoordinates(city) {
        city = normalizeString(city);
        const apiUrl = `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(city)},VN&limit=1&appid=${apiKey}`;

        try {
            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error(`Không thể lấy tọa độ: ${response.statusText}`);
            const data = await response.json();

            if (data.length === 0) {
                console.warn(`Không tìm thấy tọa độ của: ${city}, sử dụng tọa độ mặc định (Hà Nội).`);
                return { city, lat: 21.0285, lon: 105.8542 }; // Tọa độ Hà Nội
            }

            return { city: data[0].name || city, lat: data[0].lat, lon: data[0].lon };
        } catch (error) {
            console.error(`Lỗi lấy tọa độ ${city}:`, error.message);
            return { city, lat: 21.0285, lon: 105.8542 };
        }
    }

    // Lấy dữ liệu thời tiết hiện tại
    async function fetchCurrentWeather(city) {
        const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric&lang=vi`;

        try {
            const response = await fetch(apiUrl);
            if (!response.ok) throw new Error("Không tìm thấy tỉnh/thành phố");

            const data = await response.json();
            return {
                name: data.name || "--",
                description: data.weather[0]?.description || "--",
                icon: `http://openweathermap.org/img/wn/${data.weather[0]?.icon}@2x.png`,
                temp: Math.round(data.main.temp),
                sunrise: new Date(data.sys.sunrise * 1000).toLocaleTimeString("vi-VN"),
                sunset: new Date(data.sys.sunset * 1000).toLocaleTimeString("vi-VN"),
                humidity: data.main.humidity,
                windSpeed: (data.wind.speed * 3.6).toFixed(2)
            };
        } catch (error) {
            console.error(`Lỗi khi lấy dữ liệu thời tiết:`, error.message);
            alert("Không tìm thấy tỉnh/thành phố!");
            return null;
        }
    }

    // Lấy dữ liệu dự báo 5 ngày và theo giờ
    async function fetchWeatherForecast(lat, lon, city) {
        const cacheKey = `forecast_${lat}_${lon}`;
        const cacheData = localStorage.getItem(cacheKey);
        const cacheTime = localStorage.getItem(`${cacheKey}_time`);
        const currentTime = Date.now();
        const cacheDuration = 10 * 60 * 1000; // 10 phút

        if (cacheData && cacheTime && (currentTime - parseInt(cacheTime) < cacheDuration)) {
            console.log("Sử dụng dữ liệu dự báo thời tiết từ cache.");
            return JSON.parse(cacheData);
        }

        try {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`);
            const data = await response.json();

            if (!data.list || data.cod !== "200") {
                throw new Error("Dữ liệu dự báo không hợp lệ!");
            }

            // Dữ liệu hàng ngày cho giao diện
            const dailyData = [];
            let currentDay = new Date().getDate();
            let dayData = [];

            // Dữ liệu theo giờ nhóm theo ngày
            const hourlyDataByDay = {};
            let currentDate = null;

            data.list.forEach(item => {
                const itemDate = new Date(item.dt_txt);
                const itemDay = itemDate.getDate();
                const dateKey = itemDate.toLocaleDateString("vi-VN");

                // Tổng hợp dữ liệu hàng ngày
                if (itemDay === currentDay) {
                    dayData.push(item);
                } else {
                    if (dayData.length > 0) {
                        dailyData.push(calculateAverage(dayData));
                    }
                    dayData = [item];
                    currentDay = itemDay;
                }

                // Dữ liệu theo giờ
                if (!hourlyDataByDay[dateKey]) {
                    hourlyDataByDay[dateKey] = [];
                }
                hourlyDataByDay[dateKey].push({
                    time: itemDate.toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" }),
                    temp: Math.round(item.main.temp),
                    description: item.weather[0].description,
                    icon: `http://openweathermap.org/img/wn/${item.weather[0].icon}.png`,
                    humidity: item.main.humidity,
                    windSpeed: (item.wind.speed * 3.6).toFixed(2)
                });
            });

            if (dayData.length > 0) {
                dailyData.push(calculateAverage(dayData));
            }

            // Dữ liệu biểu đồ (giữ lại cho sử dụng sau)
            const labels = [];
            const temperatures = [];
            const rainfall = [];
            const humidity = [];
            const windSpeed = [];

            for (let i = 0; i < data.list.length; i += 8) {
                const date = new Date(data.list[i].dt * 1000);
                labels.push(date.toLocaleDateString("vi-VN"));
                temperatures.push(data.list[i].main.temp);
                rainfall.push(data.list[i].rain ? data.list[i].rain["1h"] || 0 : 0);
                humidity.push(data.list[i].main.humidity);
                windSpeed.push(data.list[i].wind.speed);
            }

            const forecastData = {
                dailyData,
                hourlyData: hourlyDataByDay,
                chartData: { labels, temperatures, rainfall, humidity, windSpeed }
            };

            localStorage.setItem(cacheKey, JSON.stringify(forecastData));
            localStorage.setItem(`${cacheKey}_time`, currentTime.toString());

            return forecastData;
        } catch (error) {
            console.error("Lỗi lấy dữ liệu dự báo thời tiết:", error);
            return { dailyData: [], hourlyData: {}, chartData: { labels: [], temperatures: [], rainfall: [], humidity: [], windSpeed: [] } };
        }
    }

    // Tính trung bình dữ liệu thời tiết cho một ngày
    function calculateAverage(dayData) {
        const avgData = {
            temp: 0,
            humidity: 0,
            windSpeed: 0,
            weather: dayData[0].weather[0]
        };

        dayData.forEach(item => {
            avgData.temp += item.main.temp;
            avgData.humidity += item.main.humidity;
            avgData.windSpeed += item.wind.speed;
        });

        avgData.temp = (avgData.temp / dayData.length).toFixed(1);
        avgData.humidity = (avgData.humidity / dayData.length).toFixed(0);
        avgData.windSpeed = (avgData.windSpeed / dayData.length).toFixed(1);

        return avgData;
    }

    // Dịch mô tả thời tiết
    function translateWeatherDescription(description) {
        return weatherDescriptions[description] || description;
    }

    // Hiển thị thời tiết từ localStorage
    function displayWeatherFromStorage(weather) {
        const cityNameEl = document.querySelector('.city-name');
        const descEl = document.querySelector('.weather-state');
        const iconEl = document.querySelector('.weather-icon');
        const tempEl = document.querySelector('.temperature');
        const sunriseEl = document.querySelector('.sunrise');
        const sunsetEl = document.querySelector('.sunset');
        const humidityEl = document.querySelector('.humidity');
        const windEl = document.querySelector('.wind-speed');

        if (cityNameEl) cityNameEl.textContent = weather.name;
        if (descEl) descEl.textContent = translateWeatherDescription(weather.description);
        if (iconEl) iconEl.src = weather.icon;
        if (tempEl) tempEl.textContent = `${weather.temp}°C`;
        if (sunriseEl) sunriseEl.textContent = weather.sunrise;
        if (sunsetEl) sunsetEl.textContent = weather.sunset;
        if (humidityEl) humidityEl.textContent = `${weather.humidity}%`;
        if (windEl) windEl.textContent = `${weather.windSpeed} km/h`;
    }

    // Cập nhật giao diện thời tiết
    function updateWeatherUI(city, data, lat, lon, chartData) {
        const weather = data.current || data;
        const translatedDescription = translateWeatherDescription(weather.weather ? weather.weather.description : weather.description);

        const cityNameEl = document.querySelector(".city-name");
        const descEl = document.querySelector(".weather-state");
        const iconEl = document.querySelector(".weather-icon");
        const tempEl = document.querySelector(".temperature");
        const sunriseEl = document.querySelector(".sunrise");
        const sunsetEl = document.querySelector(".sunset");
        const humidityEl = document.querySelector(".humidity");
        const windEl = document.querySelector(".wind-speed");

        if (cityNameEl) cityNameEl.textContent = city;
        if (descEl) descEl.textContent = translatedDescription;
        if (iconEl) iconEl.src = weather.weather ? `https://openweathermap.org/img/wn/${weather.weather.icon}@2x.png` : weather.icon;
        if (tempEl) tempEl.textContent = `${weather.temp}°C`;
        if (sunriseEl && weather.sunrise) sunriseEl.textContent = weather.sunrise;
        if (sunsetEl && weather.sunset) sunsetEl.textContent = weather.sunset;
        if (humidityEl) humidityEl.textContent = `${weather.humidity}%`;
        if (windEl) windEl.textContent = `${weather.windSpeed} km/h`;

        // Cập nhật tiêu đề bảng dự báo theo giờ
        if (cityNameInHourlyTable) {
            cityNameInHourlyTable.textContent = city;
        }

        // Cập nhật bảng dự báo theo giờ
        updateHourlyTable(0); // Mặc định là hôm nay
    }

    // Cập nhật bảng dự báo theo giờ
    function updateHourlyTable(dayIndex) {
        if (!currentHourlyData || !hourlyTableBody) return;

        const today = new Date();
        today.setDate(today.getDate() + dayIndex);
        const dateKey = today.toLocaleDateString("vi-VN");
        const hourlyData = currentHourlyData[dateKey] || [];

        hourlyTableBody.innerHTML = ""; // Xóa các hàng hiện tại

        if (hourlyData.length === 0) {
            const row = document.createElement("tr");
            row.innerHTML = `<td colspan="6" class="text-center">Không có dữ liệu cho ngày này</td>`;
            hourlyTableBody.appendChild(row);
            return;
        }

        hourlyData.forEach(hour => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${hour.time}</td>
                <td><img src="${hour.icon}" alt="weather icon" style="width: 40px;"></td>
                <td>${hour.temp}°C</td>
                <td>${translateWeatherDescription(hour.description)}</td>
                <td>${hour.humidity}%</td>
                <td>${hour.windSpeed} km/h</td>
            `;
            hourlyTableBody.appendChild(row);
        });
    }
    // Chức năng hiển thị dữ liệu dự báo theo giờ
    function displayHourlyForecast(hourlyData) {
        const hourlyTableBody = document.querySelector('#hourly-forecast-table tbody');
        hourlyTableBody.innerHTML = ""; // Clear dữ liệu cũ

        hourlyData.forEach(hour => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${hour.time}</td>
                <td><img src="${hour.icon}" alt="icon" style="width: 40px; height: 40px;"></td>
                <td>${hour.temp}°C</td>
                <td>${hour.description}</td>
                <td>${hour.humidity}%</td>
                <td>${hour.windSpeed} km/h</td>
            `;
            hourlyTableBody.appendChild(row);
        });
    }

    // Gán sự kiện cho các nút dự báo
    function attachForecastButtonEvents() {
        forecastButtons.forEach((button, index) => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);

            newButton.addEventListener("click", function () {
                forecastButtons.forEach(btn => {
                    btn.classList.remove("active");
                    btn.style.backgroundColor = "#FFEB3B";
                    btn.style.color = "black";
                    if (currentForecastData) {
                        const daysFromToday = parseInt(button.dataset.day) || 0;
                        renderForecastForDay(daysFromToday); // dùng currentForecastData đã có
                    }
                });
                this.classList.add("active");
                this.style.backgroundColor = "#007BFF";
                this.style.color = "white";

                if (currentForecastData && currentForecastData.dailyData[index]) {
                    updateWeatherUI(currentCity, currentForecastData.dailyData[index], currentLat, currentLon, currentForecastData.chartData);
                    updateHourlyTable(index); // Cập nhật bảng dự báo theo giờ cho ngày được chọn
                }
            });
        });

        // Đặt nút "Hôm nay" làm mặc định
        forecastButtons.forEach(button => {
            if (button.dataset.day === "0") {
                button.classList.add("active");
                button.style.backgroundColor = "#007BFF";
                button.style.color = "white";
            }
        });
    }

    // Hàm chính để lấy dữ liệu thời tiết và dự báo cho một tỉnh/thành phố
    async function fetchWeather(city) {
        const currentWeather = await fetchCurrentWeather(city);
        if (!currentWeather) return;

        const { city: cityName, lat, lon } = await getCityCoordinates(city);
        const forecastData = await fetchWeatherForecast(lat, lon, cityName);

        const combinedData = {
            current: currentWeather,
            forecast: forecastData.dailyData,
            chartData: forecastData.chartData
        };

        // Cập nhật các biến toàn cục
        currentCity = cityName;
        currentLat = lat;
        currentLon = lon;
        currentForecastData = forecastData;
        currentHourlyData = forecastData.hourlyData;

        // Lưu vào localStorage
        localStorage.setItem("currentCity", cityName);
        localStorage.setItem("currentLat", lat);
        localStorage.setItem("currentLon", lon);
        localStorage.setItem("forecastData", JSON.stringify(forecastData));

        // Cập nhật giao diện
        updateWeatherUI(cityName, combinedData, lat, lon, forecastData.chartData);

        // Gán sự kiện cho các nút dự báo
        attachForecastButtonEvents();
    }

    // Lấy dữ liệu thời tiết cho nhiều tỉnh/thành phố
    async function fetchWeatherByCities(cityList) {
        const cities = cityList.split(",").map(city => city.trim());
        const coordinatesList = await Promise.all(cities.map(getCityCoordinates));

        for (const { city, lat, lon } of coordinatesList) {
            console.log(`Lấy dữ liệu thời tiết cho ${city}:`);
            await fetchWeather(city);
        }
    }

    // Khởi tạo khi trang tải
    const saved = localStorage.getItem("selectedCity");
    if (saved) {
        const weather = JSON.parse(saved);
        displayWeatherFromStorage(weather);
        fetchWeather(weather.name);
        localStorage.removeItem("selectedCity");
    } else {
        fetchWeather(DEFAULT_CITY);
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
    }
});
