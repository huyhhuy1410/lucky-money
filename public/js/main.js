import MobileModule from "./module/MobileModule.js";
import PopupModule from "./module/PopupModule.js";
import ScrollTriggerModule from "./module/ScrollTriggerModule.js";
import ComponentModule from "./module/ComponentModule.js";
import lixiModule from "./module/lixiModule.js";
// import toastAction from "./module/ToastModule.js";
window.addEventListener("DOMContentLoaded", () => {
    // Animation
    ScrollTriggerModule();
    // Loadmore
    // Tab
    // Upload File
    // DateTime
    // PlusMinus
    // Select
    // CountUP
    // Component
    MobileModule();
    PopupModule();
    ComponentModule();
    // 
    lixiModule();
});