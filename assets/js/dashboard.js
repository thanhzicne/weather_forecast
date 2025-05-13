// =======================Lịch========================
document.addEventListener("DOMContentLoaded", function () {
    flatpickr("#calendar", {
        inline: true,  
        dateFormat: "F Y",  
        defaultDate: new Date(),  
        locale: "vn"  
    });
});
// ===========================Thời gian thực======================================
function updateTime() {
    const now = new Date();
    const options = { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    };

    const formattedTime = now.toLocaleTimeString('vi-VN', options);
    document.getElementById("local-time").textContent = formattedTime.replace(',', '');
}

// Cập nhật thời gian mỗi giây
setInterval(updateTime, 1000);
// Gọi ngay khi trang load
updateTime();

// =========================Nút đẩy lên đầu trang========================================
document.addEventListener("DOMContentLoaded", function () {
    let backToTop = document.getElementById("backToTop");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 200) {
            backToTop.style.display = "flex";
        } else {
            backToTop.style.display = "none";
        }
    });

    backToTop.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});