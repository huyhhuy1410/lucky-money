export function Noti({
  icon = "success",
  text,
  title,
  timer = 4000,
  redirect = "",
}) {
  const mainElement = document.querySelector("#wpbody");
  const styleId = "dynamic-progress-style"; // Unique ID for the style element
  // Check if the style element already exists
  let existingStyle = document.getElementById(styleId);

  if (existingStyle) {
    // If it exists, remove it
    existingStyle.remove();
  }
  // Create a new style element
  const style = document.createElement("style");
  style.id = styleId; // Set the unique ID
  style.textContent = `
  .noti_alert .progress.active:before { 
    animation: progress ${timer / 1000}s linear forwards; 
  }
`;

  // Append the new style element to the head
  document.head.appendChild(style);
  var noti_con = document.querySelector(".noti_con");
  if (!noti_con) {
    var noti_con = document.createElement("div");
    noti_con.setAttribute("class", "noti_con");
    mainElement.appendChild(noti_con);
  }

  var noti_alert = document.createElement("div");
  var noti_icon = document.createElement("div");
  var noti_process = document.createElement("div");
  noti_icon.setAttribute("class", "noti_icon " + icon);
  noti_alert.setAttribute("class", "noti_alert");
  noti_process.setAttribute("class", "progress active " + icon);
  noti_alert.innerHTML =
    '<div class="message"><div class="text1">' +
    title +
    '</div><div class="text2">' +
    text +
    "</div></div>";
  noti_alert.prepend(noti_icon);
  noti_alert.prepend(noti_process);
  noti_con.prepend(noti_alert);

  if (icon == "success") {
    // noti_icon.style.background = '#00b972';
    noti_icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="60" stroke-dashoffset="60" d="M3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="60;0"/></path><path stroke-dasharray="14" stroke-dashoffset="14" d="M8 12L11 15L16 10"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.2s" values="14;0"/></path></g></svg>`;
  } else if (icon == "info") {
    // noti_icon.style.background = '#0395FF';
    noti_icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"><path stroke-dasharray="60" stroke-dashoffset="60" d="M12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="60;0"/></path><path stroke-dasharray="20" stroke-dashoffset="20" d="M8.99999 10C8.99999 8.34315 10.3431 7 12 7C13.6569 7 15 8.34315 15 10C15 10.9814 14.5288 11.8527 13.8003 12.4C13.0718 12.9473 12.5 13 12 14"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.4s" values="20;0"/></path></g><circle cx="12" cy="17" r="1" fill="currentColor" fill-opacity="0"><animate fill="freeze" attributeName="fill-opacity" begin="1s" dur="0.2s" values="0;1"/></circle></svg>`;
  } else if (icon == "danger" || icon == "error") {
    // noti_icon.style.background = '#FF032C';
    noti_icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"><path stroke-dasharray="60" stroke-dashoffset="60" d="M12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="60;0"/></path><path stroke-dasharray="8" stroke-dashoffset="8" d="M12 12L16 16M12 12L8 8M12 12L8 16M12 12L16 8"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.2s" values="8;0"/></path></g></svg>`;
  } else {
    // noti_icon.style.background = '#00b972';
    noti_icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="60" stroke-dashoffset="60" d="M3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="60;0"/></path><path stroke-dasharray="14" stroke-dashoffset="14" d="M8 12L11 15L16 10"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.2s" values="14;0"/></path></g></svg>`;
  }

  setTimeout(() => {
    noti_alert.classList.add("active");
  }, 100);

  setTimeout(() => {
    noti_alert.classList.remove("active");
  }, timer);

  setTimeout(() => {
    noti_alert.remove();
  }, timer + 2000);
}

export function ajaxPostData(formdata, processing) {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: lm_ajax_url.ajaxURL,
      type: "POST",
      data: Object.fromEntries(formdata),
      beforeSend: function (data) {
        processing.addClass("loading");
      },
      success: function (data) {
        resolve(data);
      },
      error: function (error) {
        reject(error);
      },
    });
  });
}

export function scrollToID(el, speed, number) {
  const offSet = jQuery("#wpwrap").outerHeight();
  const section = jQuery(el).offset();
  const targetOffset = section.top - offSet - number;
  jQuery("html,body").animate({ scrollTop: targetOffset }, speed, "swing");
}
