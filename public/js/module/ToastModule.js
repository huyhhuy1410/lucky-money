// Toast function
export function ToastModule({ type = "info", content = "", duration = 3000 }) {
  const main = document.getElementById("lucky-money-toast");
  if (main) {
    const delay = (duration / 1000).toFixed(2);
    const toast = document.createElement("div");
    const styleId = "dynamic-progress-style"; // Unique ID for the style element
    let existingStyle = document.getElementById(styleId);

    if (existingStyle) {
      // If it exists, remove it
      existingStyle.remove();
    }
    // Create a new style element
    const style = document.createElement("style");
    style.id = styleId; // Set the unique ID
    style.textContent = `
    .toast-wrap .progress:before { animation: progress ${delay}s linear forwards; }
  `;
    // Append the new style element to the head
    document.head.appendChild(style);
    // Auto remove toast
    const autoRemoveId = setTimeout(function () {
      main.removeChild(toast);
    }, duration + 1000);

    // Remove toast when clicked
    toast.onclick = function (e) {
      if (e.target.closest(".toast__close")) {
        main.removeChild(toast);
        clearTimeout(autoRemoveId);
      }
    };

    toast.classList.add("toast-wrap", `toast--${type}`);
    toast.style.animation = `slideInLeft ease .3s, fadeOut linear 1s ${delay}s forwards`;

    toast.innerHTML = `${content}`;
    main.appendChild(toast);
  }
}
