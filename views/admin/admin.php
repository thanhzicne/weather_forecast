<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dự báo thời tiết</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        h1 {
            color: #007bff;
            font-weight: 600;
        }
        .nav-tabs .nav-link {
            color: #007bff;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .nav-tabs .nav-link:hover {
            background-color: #007bff;
            color: white;
        }
        .nav-tabs .nav-link.active {
            background-color: #0056b3;
            color: white;
        }
        .tab-pane {
            padding-top: 20px;
        }
        table {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 20px;
        }
        th {
            background-color: #f8f9fa;
            color: #007bff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-danger {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        #backToTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            padding: 10px 15px;
            font-size: 18px;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }
        #backToTop:hover {
            background-color: #0056b3;
        }
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Trang quản lý (Admin)</h1>
    <div class="text-end mb-3">
        <a href="/weather_forecast/logout" class="btn btn-danger">Thoát</a>
    </div>
    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php echo ($activeTab === 'alerts') ? 'active' : ''; ?>" id="alerts-tab" data-bs-toggle="tab" href="#alerts" role="tab">Quản lý bài báo</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($activeTab === 'feedback') ? 'active' : ''; ?>" id="feedback-tab" data-bs-toggle="tab" href="#feedback" role="tab">Phản hồi người dùng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($activeTab === 'weather') ? 'active' : ''; ?>" id="weather-tab" data-bs-toggle="tab" href="#weather" role="tab">Dữ liệu thời tiết</a>
        </li>
    </ul>
        <!--  -->
    <div class="tab-content" id="adminTabContent">
        <!-- Tab Quản lý bài báo -->
        <div class="tab-pane fade <?php echo ($activeTab === 'alerts') ? 'show active' : ''; ?>" id="alerts" role="tabpanel">
            <h3 class="mt-4">Quản lý bài báo</h3>
            <?php
            require_once __DIR__ . '/../../controllers/AlertController.php';
            $alertController = new AlertController();
            $alerts = json_decode($alertController->getAll(), true);

            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Đường dẫn file</th>
                        <th>Thời gian bắt đầu</th>
                        <th>Thời gian kết thúc</th>
                        <th>Người tạo</th>
                        <th>Thời gian tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="alertsTable">
                    <?php if (empty($alerts)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có bài báo nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($alerts as $alert): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alert['id']); ?></td>
                                <td><?php echo htmlspecialchars($alert['title']); ?></td>
                                <td><?php echo htmlspecialchars($alert['file_path'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($alert['start_time']); ?></td>
                                <td><?php echo htmlspecialchars($alert['end_time']); ?></td>
                                <td><?php echo htmlspecialchars($alert['username'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($alert['created_at']); ?></td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $alert['id']; ?>">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tab Phản hồi người dùng -->
        <div class="tab-pane fade <?php echo ($activeTab === 'feedback') ? 'show active' : ''; ?>" id="feedback" role="tabpanel">
            <h3 class="mt-4">Phản hồi người dùng</h3>
            <?php
            require_once __DIR__ . '/../../controllers/FeedbackController.php';
            $feedbackController = new FeedbackController();
            $feedbacks = $feedbackController->getAll();

            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Nội dung</th>
                        <th>Thời gian gửi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feedbacks)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Không có phản hồi nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['email']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['content']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tab Dữ liệu thời tiết -->
        <div class="tab-pane fade <?php echo ($activeTab === 'weather') ? 'show active' : ''; ?>" id="weather" role="tabpanel">
            <h3 class="mt-4">Dữ liệu thời tiết</h3>
            <?php
            require_once __DIR__ . '/../../controllers/WeatherController.php';
            $weatherController = new WeatherController();

            if (isset($_POST['save_weather'])) {
                $weatherController->saveWeather();
            }

            $weatherData = $weatherController->getWeatherHistory();

            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <form method="post" class="mb-3">
                <button type="submit" name="save_weather" class="btn btn-primary">Lưu thời tiết ngày hiện tại</button>
            </form>
            <!-- Form lọc thời gian -->
            <form method="get" class="mb-3">
                <input type="hidden" name="tab" value="weather">
                <div class="row">
                    <div class="col-md-3">
                        <label for="start_date">Từ ngày:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">Đến ngày:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-info">Lọc</button>
                        <a href="?tab=weather" class="btn btn-secondary">Xóa bộ lọc</a>
                    </div>
                </div>
            </form>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Thành phố</th>
                        <th>Ngày đã lưu</th>
                        <th>Nhiệt độ (°C)</th>
                        <th>Độ ẩm (%)</th>
                        <th>Điều kiện</th>
                        <th>Tốc độ gió (m/s)</th>
                        <th>Thời gian cập nhật</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($weatherData)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có dữ liệu thời tiết.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($weatherData as $weather): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($weather['city']); ?></td>
                                <td><?php echo htmlspecialchars($weather['action_date'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($weather['temperature']); ?></td>
                                <td><?php echo htmlspecialchars($weather['humidity']); ?></td>
                                <td><?php echo htmlspecialchars($weather['condition']); ?></td>
                                <td><?php echo htmlspecialchars($weather['wind_speed']); ?></td>
                                <td><?php echo htmlspecialchars($weather['recorded_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<button id="backToTop" class="btn btn-primary rounded-circle shadow">
    <i class="bi bi-chevron-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
<script>

    window.onscroll = function() {
        if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
            document.getElementById("backToTop").style.display = "block";
        } else {
            document.getElementById("backToTop").style.display = "none";
        }
    };

    document.getElementById("backToTop").onclick = function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('Bạn có chắc chắn muốn xóa bài báo này?')) return;

            const id = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('id', id);

            fetch('/weather_forecast/controllers/AlertController.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const urlParams = new URLSearchParams(window.location.search);
                    const activeTab = urlParams.get('tab') || 'alerts';
                    window.location.href = `?tab=${activeTab}`;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa bài báo.');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'alerts';
        const tabElement = document.querySelector(`#${activeTab}-tab`);
        if (tabElement) {
            new bootstrap.Tab(tabElement).show();
        }
    });
</script>
</body>
</html>