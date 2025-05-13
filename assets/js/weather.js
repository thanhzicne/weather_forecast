const API_KEY = "e2c222797835642c23ca9f8d6fda7d2b";
const API_URL = "https://api.openweathermap.org/data/2.5/forecast?lat={lat}&lon={lon}&units=metric&appid={API_KEY}";

async function fetchWeatherData(lat, lon) {
    try {
        const response = await fetch(`https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=metric&appid=${API_KEY}`);
        const data = await response.json();

        if (!data.list) {
            throw new Error("Dữ liệu không hợp lệ!");
        }

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

        return { labels, temperatures, rainfall, humidity, windSpeed }; 
    } catch (error) {
        console.error("Lỗi lấy dữ liệu thời tiết:", error);
        return { labels: [], temperatures: [], rainfall: [], humidity: [], windSpeed: [] };
    }
}

function normalizeString(str) {
    return str
        .normalize("NFD") // Tách dấu khỏi ký tự
        .replace(/[\u0300-\u036f]/g, "") // Loại bỏ dấu
        .replace(/\s+/g, " ") // Xóa khoảng trắng dư thừa
        .trim(); // Loại bỏ khoảng trắng đầu & cuối
}

// Hàm lấy tọa độ của một tỉnh/thành phố từ OpenWeather API
async function getCityCoordinates(city) {
    city = normalizeString(city); // Chuẩn hóa tên thành phố trước khi gửi API
    const API_URL = `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(city)},VN&limit=1&appid=${API_KEY}`;

    try {
        const response = await fetch(API_URL);
        const data = await response.json();

        if (data.length === 0) {
            console.warn(`Không tìm thấy tọa độ của: ${city}, sử dụng tọa độ mặc định (Hà Nội).`);
            return { city, lat: 21.0285, lon: 105.8542 }; // Mặc định là Hà Nội nếu lỗi
        }

        return { city, lat: data[0].lat, lon: data[0].lon };
    } catch (error) {
        console.error(`Lỗi lấy tọa độ ${city}:`, error);
        return { city, lat: 21.0285, lon: 105.8542 }; // Nếu lỗi, dùng Hà Nội
    }
}

// Hàm tìm kiếm thời tiết cho nhiều tỉnh/thành phố
async function fetchWeatherByCities(cityList) {
    const cities = cityList.split(",").map(city => city.trim()); // Tách danh sách các tỉnh
    const coordinatesList = await Promise.all(cities.map(getCityCoordinates)); // Lấy tọa độ cho từng thành phố

    coordinatesList.forEach(({ city, lat, lon }) => {
        console.log(`Dự báo thời tiết cho ${city}:`);
        fetchWeatherData(lat, lon);
    });
}
