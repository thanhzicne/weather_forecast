document.addEventListener("DOMContentLoaded", async function () {
    const cityInput = document.getElementById("cityInput");
    const searchButton = document.getElementById("searchButton");
    let weatherChart;

    // Mặc định thanh tìm kiếm là Hà Nội
    cityInput.value = "Hanoi";

    // Sự kiện khi nhấn nút tìm kiếm
    searchButton.addEventListener("click", () => handleSearch());

    // Sự kiện khi nhấn Enter trong ô nhập
    cityInput.addEventListener("keypress", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            handleSearch();
        }
    });

    // Xử lý tìm kiếm thời tiết
    async function handleSearch() {
        const city = cityInput.value.trim();
        if (city) {
            try {
                const { lat, lon } = await getCityCoordinates(city);
                const weatherData = await fetchWeatherData(lat, lon);

                console.log("Dữ liệu thời tiết:", weatherData);

                if (weatherData && weatherData.rainfall && weatherData.rainfall.length > 0) {
                    updateChart(weatherData);
                } else {
                    alert("Không có dữ liệu thời tiết hoặc lượng mưa cho thành phố này.");
                }
            } catch (error) {
                console.error("Lỗi khi lấy dữ liệu:", error);
                alert("Không thể lấy dữ liệu thời tiết. Vui lòng thử lại!");
            }
        }
    }

    // Hàm cập nhật biểu đồ
    function updateChart(data) {
        const ctx = document.getElementById("weatherChart").getContext("2d");
        
        // Xóa biểu đồ cũ nếu đã tồn tại
        if (weatherChart) {
            weatherChart.destroy();
        }

        // Khởi tạo biểu đồ mới
        weatherChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: "Nhiệt độ (°C)",
                        data: data.temperatures,
                        type: "line",
                        borderColor: "red",
                        backgroundColor: "rgba(255, 99, 132, 0.2)",
                        borderWidth: 2,
                        fill: false,
                        yAxisID: "y",
                        pointRadius: 5
                    },
                    {
                        label: "Lượng mưa (mm)",
                        data: data.rainfall,
                        backgroundColor: "rgba(0, 0, 255, 0.5)",
                        borderColor: "blue",
                        borderWidth: 1,
                        barThickness: 20, 
                        yAxisID: "y1"
                    },
                    {
                        label: "Độ ẩm (%)",
                        data: data.humidity,
                        backgroundColor: "green",
                        borderColor: "green",
                        borderWidth: 2,
                        type: "line",
                        fill: false,
                        yAxisID: "y2",
                        pointRadius: 5
                    },
                    {
                        label: "Tốc độ gió (m/s)",
                        data: data.windSpeed,
                        backgroundColor: "orange",
                        borderColor: "orange",
                        borderWidth: 2,
                        type: "line",
                        fill: false,
                        yAxisID: "y3",
                        pointRadius: 5
                    }
                ]
            },
            options: getChartOptions()
        });
    }

    // Cấu hình tùy chọn biểu đồ
    function getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: { display: true, text: "Thời gian" },
                    grid: { display: false }
                },
                y: {
                    position: "left",
                    title: { display: true, text: "Nhiệt độ (°C)" },
                    grid: { drawOnChartArea: true }
                },
                y1: {
                    position: "right",
                    title: { display: true, text: "Lượng mưa (mm)" },
                    grid: { drawOnChartArea: false },
                    min: 0,
                    max: 400 
                },
                y2: {
                    position: "right",
                    title: { display: true, text: "Độ ẩm (%)" },
                    grid: { drawOnChartArea: false },
                    min: 0,
                    max: 100
                },
                y3: {
                    position: "right",
                    title: { display: true, text: "Tốc độ gió (m/s)" },
                    grid: { drawOnChartArea: false },
                    min: 0
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            let label = tooltipItem.dataset.label || "";
                            let value = tooltipItem.raw;
                            return `${label}: ${value}`;
                        }
                    }
                },
                legend: {
                    labels: { font: { size: 12 } }
                }
            }
        };
    }

    handleSearch();
});