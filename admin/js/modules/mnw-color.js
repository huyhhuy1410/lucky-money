export default function ColorPicker() {
    function hexToColorName(hex) {
        const colors = {
            "#ff0000": "Red",
            "#00ff00": "Green",
            "#0000ff": "Blue",
            "#ffff00": "Yellow",
            "#ffa500": "Orange",
            "#800080": "Purple",
            "#ffffff": "White",
            "#000000": "Black",
            "#808080": "Gray",
            "#00ffff": "Cyan"
        };
        return colors[hex.toLowerCase()] || "Lucky Money Color";
    }

    // Hàm xử lý sự kiện khi chọn màu
    // function setupColorPickers() {
    //     const colorPickers = document.querySelectorAll(".colorPicker"); // Lấy tất cả các color picker
    //     if (colorPickers) {
    //         colorPickers.forEach((picker) => {
    //             picker.addEventListener("change", function () {
    //                 const selectedColor = picker.value; // Lấy giá trị mã HEX của màu
    //                 // const colorName = hexToColorName(selectedColor); // Chuyển mã HEX sang tên màu
    //                 const colorNameInput = picker.closest(".mnw-color-item").querySelector(".colorName"); // Ô input tiếp theo để hiển thị tên màu
    //                 const colorSqr = picker.closest(".mnw-color-item").querySelector(".mnw-color-label")
    //                 colorSqr.style.background = selectedColor;
    //                 colorNameInput.value = `${selectedColor})`; // Cập nhật giá trị
    //             });
    //         });
    //     }
    // }
    // setupColorPickers();

    // 
    // const inputUploads = document.querySelectorAll(".upload-image");
    // inputUploads.forEach((inputUpload) => {
    //     inputUpload.addEventListener("change", function (event) {
    //         const previewContainer = this.closest(".preview-container"); // Xác định vùng chứa liên quan
    //         if (previewContainer) {
    //             const image = previewContainer.querySelector(".preview-img img");
    //             if (image) {
    //                 const file = event.target.files[0];
    //                 if (file) {
    //                     image.src = URL.createObjectURL(file);
    //                     image.srcset = URL.createObjectURL(file);
    //                 }
    //             }
    //         }
    //     });
    // });
    const circles = document.querySelectorAll(".circle");
    if (circles) {
        circles.forEach((circle) => {
            const value = parseFloat(circle.getAttribute("data-value")); // Lấy giá trị từ data-value
            const progressCircle = circle.querySelector(".js-progress-bar");

            if (progressCircle) {
                const radius = progressCircle.r.baseVal.value; // Bán kính của vòng tròn
                const circumference = 2 * Math.PI * radius; // Chu vi của vòng tròn

                progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
                progressCircle.style.strokeDashoffset = circumference;

                // Tính toán offset dựa trên giá trị (0-100%)
                const offset = circumference - (value / 100) * circumference;

                // Thêm animation
                progressCircle.style.transition = "stroke-dashoffset 1s ease-out";
                progressCircle.style.strokeDashoffset = offset;
            }
        });
    }
}