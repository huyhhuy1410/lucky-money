export default function ComponentModule() {
  // Funtion Copppy
  const btnCoppy = document.querySelectorAll(".center-control-btn.coppy-link");
  if (btnCoppy) {
    btnCoppy.forEach((item) => {
      item.addEventListener("click", (e) => {
        btnCoppy.forEach((item2) => {
          item2.querySelector(".text").innerHTML =
            item.getAttribute("data-csc");
          item2.classList.remove("active");
        });
        e.preventDefault();
        const value = item.href;
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(value).select();
        document.execCommand("copy");
        $temp.remove();
        item.querySelector(".text").innerHTML = item.getAttribute("data-dsc");
        item.classList.add("active");
      });
    });
  }

  // check pass;
  const fPass = document.querySelectorAll(".f-pass");
  if (fPass) {
    fPass.forEach((item) => {
      item.addEventListener("click", (e) => {
        const input = item.querySelector(".re-input");
        if (e.target.closest(".f-lock")) {
          if (input.type == "text") {
            input.type = "password";
            item.classList.remove("active");
          } else {
            input.type = "text";
            item.classList.add("active");
          }
        }
      });
    });
  }
  // svg run
  const pathElement = document.querySelectorAll(".pathElement");
  if (pathElement) {
    pathElement.forEach(item => {
      let dataPath = item.dataset.path;
      function updateOffsetPath() {
        const viewportWidth = window.innerWidth;
        const pathMultiplier = viewportWidth / 1728;
        const newPathValue = dataPath.replace(/(\d+\.\d+)/g, match => parseFloat(match) * pathMultiplier);
        item.style = `--path: path("${newPathValue}")`;
      }
      window.addEventListener('resize', updateOffsetPath);
      updateOffsetPath();
    }
    )
  }
  // 

  const files = document.getElementById("inputFile")
  if (files) {
    files.addEventListener("change", function () {
      var file = this.files[0];
      if (file) {
        var imageNameSpan = document.getElementById("imageName");
        imageNameSpan.textContent = file.name;
      }
    });
  }

  const speed = 300;
  // NẾU CÓ ĐỊA CHỈ ID TRÊN THANH URL THÌ SCROLL XUỐNG
  const hash = window.location.hash;
  if ($(hash).length) scrollToID(hash, speed);
  // TÌM ĐỊA CHỈ ID VÀ SCROLL XUỐNG NẾU CÓ CLASS
  $('.homes-scroll-item').on('click', function (e) {
    e.preventDefault();

    const href = $(this).find('> a').attr('href') || $(this).attr('href');
    const id = href.slice(href.lastIndexOf('#'));
    if ($(id).length) {
      scrollToID(id, speed);
    } else {
      // window.location.replace(/${id});
      window.location.href = href;
    }
  });
  // HÀM SCROLL CHO MƯỢT MÀ
  function scrollToID(id, speed) {
    const offSet = $('.hd').outerHeight();
    const section = $(id).offset();
    const targetOffset = section.top - offSet + 25;
    $('html,body').animate({ scrollTop: targetOffset }, speed);
  }
  // 
  // Copy
  const copyBtn = document.querySelector(".copyJS")
  if (copyBtn) {
    const copyTxt = copyBtn.querySelector(".txt")
    copyBtn.addEventListener("click", () => {
      copyUrl();
    })
    function copyUrl() {
      const url = window.location.href; // Lấy URL hiện tại
      navigator.clipboard.writeText(url)
        .then(() => {
          copyBtn.classList.add("active");
          setTimeout(() => {
            copyBtn.classList.remove("active");
          }, 500)
        })
    }
  }

  if (window.innerWidth > 992) {
    const svBoxs = document.querySelectorAll(".mbJS")
    if (svBoxs) {
      svBoxs.forEach(svBox => {
        const svBoxItems = svBox.querySelectorAll(".svBox-it")
        svBoxItems.forEach(svBoxItem => {
          const svBoxItemH = svBoxItem.clientHeight;
          svBox.style.marginTop = (-1 * svBoxItemH) / 2 + "px";
        })
      })
    }
  }

}
