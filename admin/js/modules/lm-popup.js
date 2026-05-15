export default function LMPopupModule() {
    const popupClose = document.querySelectorAll(".lm-popup-close");
    const popupOverlay = document.querySelectorAll(".lm-popup-overlay");
    const body = document.getElementsByTagName("body")[0];
    const popup = document.querySelectorAll(".lm-popup");
    if (popupClose) {
        popupClose.forEach((item) => {
            item.addEventListener("click", () => {
                const parentPopup = item.closest(".lm-popup");
                // console.log(parentPopup);
                if (parentPopup && parentPopup.classList.contains("open")) {
                    parentPopup.classList.remove("open");
                    body.classList.remove("no-scroll");
                }
            });
        });
    }
    if (popupOverlay) {
        popupOverlay.forEach((item) => {
            item.addEventListener("click", () => {
                const parentPopup = item.closest(".lm-popup");
                parentPopup.classList.remove("open");
                body.classList.remove("no-scroll");
            });
        });
    }

    const popupOpens = document.querySelectorAll(".lm-popup-open");
    if (popupOpens) {
        popupOpens.forEach((item) => {
            item.addEventListener("click", (e) => {
                e.preventDefault();
                const idString = item.getAttribute("data-popup");
                if (popup) {
                    popup.forEach((item) => {
                        if (item.getAttribute("data-popup-id") == idString) {
                            item.classList.add("open");
                            body.classList.add("no-scroll");
                        }
                    });
                }
            });
        }); 
    }
}