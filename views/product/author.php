<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <main class="container mt-5">
        <!-- Breadcrumb -->
        <div class="container mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-dark" href="/weather_forecast/views/layouts/main.php">Trang chủ</a></li>
                <span class="separator">&nbsp;»&nbsp; </span>
                <li class="breadcrumb-item active" aria-current="page">Tác giả</li>
            </ol>
        </div>
        <h2 class="text-center mb-4 fw-bold">🌟 Giới thiệu về nhóm phát triển 🌟</h2>
        <div class="row justify-content-center">
            <!-- Thành viên 1 -->
            <div class="col-md-4">
                <div class="card text-center shadow-lg border-0 p-3">
                    <img src="../images/team/member1.jpg" class="rounded-circle mx-auto d-block border" alt="Thành viên 1" width="150" height="150">
                    <h4 class="mt-3">Phạm Đức Thành</h4>
                    <p class="text-muted">2280602956</p>
                </div>
            </div>

            <!-- Thành viên 2 -->
            <div class="col-md-4">
                <div class="card text-center shadow-lg border-0 p-3">
                    <img src="../images/team/member2.jpg" class="rounded-circle mx-auto d-block border" alt="Thành viên 2" width="150" height="150">
                    <h4 class="mt-3">Võ Trường Huy</h4>
                    <p class="text-muted">2280601278</p>
                </div>
            </div>

            <!-- Thành viên 3 -->
            <div class="col-md-4">
                <div class="card text-center shadow-lg border-0 p-3">
                    <img src="../images/team/member3.jpg" class="rounded-circle mx-auto d-block border" alt="Thành viên 3" width="150" height="150">
                    <h4 class="mt-3">Giáp Trọng Hiếu</h4>
                    <p class="text-muted">2280600941</p>
                </div>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/../partials/footer.php'; ?>