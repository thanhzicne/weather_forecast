<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <main class="container mt-5">
        <!-- Breadcrumb -->
        <div class="container mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-dark" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
                <span class="separator"> » </span>
                <li class="breadcrumb-item active" aria-current="page">Tác giả</li>
            </ol>
        </div>
        <h2 class="text-center mb-4 fw-bold">🌟 Giới thiệu về nhóm phát triển 🌟</h2>
        <div class="row justify-content-center">
            <!-- Thành viên 1 -->
            <div class="col-md-4">
                <div class="card text-center shadow-lg border-0 p-3">
                    <img src="../images/team/member1.jpg" class="rounded-circle mx-auto d-block border" alt="Thành viên 1" width="150" height="150">
                    <h4 class="mt-3">Nguyễn Văn A</h4>
                    <p class="text-muted">Backend Developer</p>
                    <p class="small">Chuyên xử lý dữ liệu và xây dựng API.</p>
                </div>
            </div>

            <!-- Thành viên 2 -->
            <div class="col-md-4">
                <div class="card text-center shadow-lg border-0 p-3">
                    <img src="../images/team/member2.jpg" class="rounded-circle mx-auto d-block border" alt="Thành viên 2" width="150" height="150">
                    <h4 class="mt-3">Trần Thị B</h4>
                    <p class="text-muted">Frontend Developer</p>
                    <p class="small">Thiết kế giao diện và tối ưu trải nghiệm người dùng.</p>
                </div>
            </div>

            <!-- Thành viên 3 -->
            <div class="col-md-4">
                <div class="card text-center shadow-lg border-0 p-3">
                    <img src="../images/team/member3.jpg" class="rounded-circle mx-auto d-block border" alt="Thành viên 3" width="150" height="150">
                    <h4 class="mt-3">Lê Văn C</h4>
                    <p class="text-muted">Data Scientist</p>
                    <p class="small">Phân tích dữ liệu và tích hợp AI/ML vào dự án.</p>
                </div>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/../partials/footer.php'; ?>