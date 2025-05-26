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
    const timeElement = document.getElementById('local-time');
    const now = new Date();
    const options = { timeZone: 'Asia/Ho_Chi_Minh', hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };
    timeElement.textContent = now.toLocaleTimeString('vi-VN', options);
    }
    setInterval(updateTime, 1000);
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