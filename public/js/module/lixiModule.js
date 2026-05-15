import { ToastModule } from "./ToastModule.js";
export default async function lixiModule() {
  const PageLixi = document.querySelector(".page-lixi");
  if (PageLixi) {
    // ======================== Game Time
    let segments = [];
    let userData = null;
    let spinning = false;
    let prizeToWin = null;
    let prizeId = null;
    let prizeType = null;
    const pageEventId = lucky_money_ajax_url.lucky_money_program;
    const canvas = document.getElementById("gameCanvas");
    if (canvas) {
      async function getDataFromAjax(page_id) {
        try {
          const response = await fetch(lucky_money_ajax_url.ajaxURL, {
            method: "POST",
            headers: {
              "Content-Type":
                "application/x-www-form-urlencoded; charset=UTF-8",
            },
            body: new URLSearchParams({
              action: "lm_ajax_program_prize_data",
              page_id: page_id,
            }),
          });
          const data = await response.json();
          if (data.success) {
            segments = data.data.segments;
            // console.log(segments);
          } else {
            console.error("Error fetching data from AJAX:", data.data);
          }
        } catch (error) {
          console.error("Error fetching data from AJAX:", error);
        }
      }
      async function getPrizeToWinAjax3(segments, page_id, prize_id) {
        try {
          const response = await fetch(lucky_money_ajax_url.ajaxURL, {
            method: "POST",
            headers: {
              "Content-Type":
                "application/x-www-form-urlencoded; charset=UTF-8",
            },
            body: new URLSearchParams({
              action: "lm_ajax_program_prize_win_3",
              page_id: page_id,
              prize_id: prize_id,
            }),
          });
          const results = await response.json();
          // lmSpinFirst.classList.remove("loading");
          if (results.success) {
            const winId = results.data.prize_id;

            prizeType = results.data.prize.prize_type;

            // const winNextId = results.data.prize_encode_next;
            // if (winNextId != undefined) {
            //   if (lmSpinFirst) {
            //     lmSpinFirst.dataset.prize_id = winNextId;
            //   }
            // }
            const prizeToWin = segments.find((segment) => segment.id === winId);
            if (prizeToWin) {
              // const resultId = results.data.result_id;
              return { prizeToWin, prizeType };
            }
          } else {
            // alert(results.data.message);
            ToastModule({
              type: "error",
              content: results.data.message,
            });
            return null;
          }
        } catch (error) {
          // Remove loading class in case of error
          console.error("Error fetching prize data:", error);
          return null;
        }
      }
      await getDataFromAjax(pageEventId);
      // console.log(segments);
      if (pageEventId != undefined) {
        // Event listener for the spin button
        const mwFormUser = document.getElementById(
          "formLucky_MoneyUserInformation"
        );
        if (mwFormUser) {
          spinning = true;
          mwFormUser.addEventListener("submit", async function (event) {
            event.preventDefault();
            // Dynamically import prizeModule
            const { getPrizeToWin, getPrizeToWinAjax } = await import(
              "../../assets/api/prizeModule.js"
            );
            userData = await getPrizeToWinAjax(pageEventId);
            if (userData) {
              document.getElementById("lm_form").classList.remove("open");
              document.getElementById("lm_game").classList.add("open");
              spinning = true;
              resizeCanvas();
            }
          });
        }
        // const primary = document.querySelector(".mnw-primary");
        // const secondary = document.querySelector(".mnw-secondary");
        const mwSpinFirst = document.getElementById("gameCanvas");
        if (mwSpinFirst) {
          prizeId = mwSpinFirst.dataset.prize_id;
          const { prizeToWin: prizeToWinz, prizeType: prizeTypez } =
            await getPrizeToWinAjax3(segments, pageEventId, prizeId);
          // if (prizeToWin) {
          //   mwSpinFirst.classList.add("disabled");
          //   spinning = true;
          // }
          // console.log(prizeToWin);
          prizeToWin = prizeToWinz;
          prizeType = prizeTypez;
          // console.log(prizeTypez);
        }

        if (prizeToWin) {
          const xoaNuts = document.querySelectorAll(".nut");
          if (xoaNuts) {
            xoaNuts.forEach((xoaNut) => {
              xoaNut.classList.remove("disabled");
            });
          }
        }
        // const mwSpinFirstGetPrize = document.querySelectorAll(
        //   ".lmSpinFirstGetPrizeJs"
        // );
        // if (mwSpinFirstGetPrize) {
        //   mwSpinFirstGetPrize.forEach(function (element) {
        //     element.addEventListener("click", function () {
        //       if (popup) {
        //         const closeButton = popup.querySelector(
        //         );
        //         if (closeButton) {
        //           closeButton.click();
        //         }
        //         window.location.reload();
        //       }
        //     });
        //   });
        // }
        // const mwSpinFirstBack = document.querySelector(".lmSpinFirstBackJs");
        // if (mwSpinFirstBack) {
        //   mwSpinFirstBack.addEventListener("click", function () {
        //     if (popup) {
        //       if (closeButton) {
        //         closeButton.click();
        //       }
        //       window.location.reload();
        //     }
        //   });
        // }
        const mwFormUser2 = document.getElementById(
          "formLucky_MoneyUserInformation2"
        );
        if (mwFormUser2) {
          mwFormUser2.addEventListener("submit", async function (event) {
            event.preventDefault();
            const { updatePrizeTicketInformationAjax } = await import(
              "../../assets/api/prizeModule.js"
            );
            const resutlt = await updatePrizeTicketInformationAjax(pageEventId);
            if (resutlt.success) {
              mwFormUser2.innerHTML = `<div class="mnw-form-success">${resutlt.data.message}</div>`;
            } else {
              ToastModule({
                type: "error",
                content: results.data.message,
              });
            }
          });
        }
        const ctx = canvas.getContext("2d");
        const cols = 4; // Số cột
        const rows = 2; // Số hàng
        const gap = 10; // Khoảng cách giữa các thẻ
        const cards = []; // Lưu thông tin thẻ
        // Dữ liệu phần thưởng duy nhất
        // const frontRewards = [
        //   {
        //     text: "Chúc mừng bạn!",
        //     image:
        //       lucky_money_ajax_url.lucky_money_dir_url +
        //       "/assets/images/aothun.png",
        //   },
        // ];
        const frontRewards = segments.map((segment) => ({
          text: segment.label,
          image: segment.image,
        }));
        // console.log(frontRewards);
        let isCardOpened = false;
        let canClickCards = false;
        // Phần thưởng cố định khi mở thẻ (lấy từ nguồn khác)
        // const popupReward = [
        //   {
        //     text: "Áo thun",
        //     image:
        //       lucky_money_ajax_url.lucky_money_dir_url +
        //       "/assets/images/aothun.png",
        //   },
        // ];
        // console.log(prizeToWin);

        const popupReward = [
          {
            text: prizeToWin ? prizeToWin.label : null, // Fallback to "Áo thun" if no description is available
            image: prizeToWin ? prizeToWin.image : null, // Fallback to default image
          },
        ];

        // rewards.sort(() => Math.random() - 0.5); // Xáo trộn phần thưởng
        // Tải hình nền
        const cardBackImage = new Image();
        cardBackImage.src =
          lucky_money_ajax_url.lucky_money_dir_url +
          "/assets/images/baolixi.jpg";
        function resizeCanvas() {
          canvas.width = canvas.offsetWidth;
          canvas.height = canvas.offsetHeight;
          setupCards();
          drawCards();
          // animateCards();
          revealAndHideCards();
        }
        function setupCards() {
          const canvasWidth = canvas.width;
          const canvasHeight = canvas.height;
          const cardWidth = (canvasWidth - (cols - 1) * gap) / cols;
          const cardHeight = (canvasHeight - (rows - 1) * gap) / rows;
          cards.length = 0;
          for (let i = 0; i < cols * rows; i++) {
            const col = i % cols;
            const row = Math.floor(i / cols);
            const x = col * (cardWidth + gap);
            const y = row * (cardHeight + gap);
            // Tải hình ảnh phần thưởng trước
            const reward = frontRewards[i % frontRewards.length];
            const rewardImage = new Image();
            rewardImage.src = reward.image;
            cards.push({
              x,
              y,
              width: cardWidth,
              height: cardHeight,
              flipped: false,
              reward: { ...reward, imageElement: rewardImage }, // Lưu ảnh đã tải
              currentX: x,
              currentY: y,
              rotationY: 0,
            });
          }
        }
        function drawCards() {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          cards.forEach((card) => {
            drawCard(
              card.currentX,
              card.currentY,
              card.width,
              card.height,
              card.flipped,
              card.reward,
              card.rotationY || 0
            );
          });
        }
        function drawRoundedRect(ctx, x, y, width, height, radius) {
          ctx.beginPath();
          ctx.moveTo(x + radius, y);
          ctx.lineTo(x + width - radius, y);
          ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
          ctx.lineTo(x + width, y + height - radius);
          ctx.quadraticCurveTo(
            x + width,
            y + height,
            x + width - radius,
            y + height
          );
          ctx.lineTo(x + radius, y + height);
          ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
          ctx.lineTo(x, y + radius);
          ctx.quadraticCurveTo(x, y, x + radius, y);
          ctx.closePath();
        }
        // Hàm vẽ thẻ
        function drawCard(
          x,
          y,
          width,
          height,
          flipped,
          reward = {},
          rotationY = 0
        ) {
          const borderRadius = 10; // Độ bo tròn góc
          ctx.save();
          // Di chuyển tâm thẻ và xoay theo trục Y
          ctx.translate(x + width / 2, y + height / 2);
          const radian = (rotationY * Math.PI) / 180;
          const scaleX = Math.cos(radian); // Tính tỉ lệ co theo X
          ctx.scale(scaleX, 1);
          ctx.translate(-(x + width / 2), -(y + height / 2));
          if (!flipped && rotationY < 90) {
            // Mặt sau thẻ
            ctx.fillStyle = "#cccccc";
            drawRoundedRect(ctx, x, y, width, height, borderRadius);
            ctx.fill();
            ctx.strokeStyle = "#fff";
            ctx.lineWidth = 1;
            ctx.stroke();
            if (cardBackImage.complete) {
              ctx.save();
              ctx.clip();
              ctx.drawImage(cardBackImage, x, y, width, height);
              ctx.restore();
            } else {
              cardBackImage.onload = () => {
                ctx.save();
                ctx.clip();
                ctx.drawImage(cardBackImage, x, y, width, height);
                ctx.restore();
              };
            }
            ctx.fillStyle = "white";
            ctx.font = `${Math.min(width, height) / 6}px Arial`;
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText("", x + width / 2, y + height / 2);
          } else if (flipped || rotationY >= 90) {
            // Mặt trước thẻ
            ctx.fillStyle = "#f39c12";
            ctx.fillStyle = "#fff";
            drawRoundedRect(ctx, x, y, width, height, borderRadius);
            ctx.fill();
            ctx.strokeStyle = "#B20E0E";
            ctx.lineWidth = 1;
            ctx.stroke();
            // Hiển thị hình ảnh phần thưởng đã tải trước
            const rewardImage = reward.imageElement;
            if (rewardImage && rewardImage.complete) {
              const imgWidth = width * 0.8;
              const imgHeight = height * 0.5;
              const imgX = x + (width - imgWidth) / 2;
              const imgY = y + height * 0.1;
              ctx.drawImage(rewardImage, imgX, imgY, imgWidth, imgHeight);
            }
            ctx.fillStyle = "black";
            ctx.font = `${Math.min(width, height) / 15}px Arial`;
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText(reward.text, x + width / 2, y + height * 0.85);
          }
          ctx.restore();
        }
        function animateCards() {
          // Giai đoạn 0: Đợi 1 giây
          setTimeout(() => {
            // Giai đoạn 1: Gom thẻ vào giữa canvas (điều chỉnh tâm chính xác)
            gsap.to(cards, {
              currentX: canvas.width / 2 - cards[0].width / 2, // Tâm chính xác (trừ nửa chiều rộng)
              currentY: canvas.height / 2 - cards[0].height / 2, // Tâm chính xác (trừ nửa chiều cao)
              duration: 1,
              onUpdate: drawCards,
              onComplete: shuffleCards,
            });
          }, 1000);
        }
        function flipCard(card, callback) {
          const flipDuration = 0.5; // Thời gian lật thẻ (giây)
          // Lật thẻ qua trục Y bằng gsap
          gsap.to(card, {
            rotationY: 90, // Xoay đến 90 độ
            duration: flipDuration / 2,
            onUpdate: () => drawCards(), // Vẽ lại trong khi lật
            onComplete: () => {
              // Sau khi xoay đến 90 độ, cập nhật trạng thái flipped
              card.flipped = true;
              card.reward = popupReward[0]; // Gán phần thưởng cố định
              // Tiếp tục xoay lại mặt trước
              gsap.to(card, {
                rotationY: 0, // Xoay trở về góc 0 độ
                duration: flipDuration / 2,
                onUpdate: () => drawCards(), // Vẽ lại trong khi lật
                onComplete: callback, // Gọi callback sau khi hoàn tất
              });
            },
          });
        }
        function shuffleCards() {
          const centerX = canvas.width / 2;
          const centerY = canvas.height / 2;
          // Khoảng cách thẻ di chuyển lên trên (đỉnh vòng cung)
          const arcHeight = 100;
          const animationDuration = 0.3; // Thời gian mỗi lá bài di chuyển
          // Lặp qua các thẻ và thực hiện hiệu ứng xào bài
          cards.forEach((card, index) => {
            const delay = (index * animationDuration) / 2; // Thời gian chờ giữa các lần di chuyển
            gsap
              .timeline()
              .to(card, {
                currentY: centerY - arcHeight, // Di chuyển lên trên (đỉnh vòng cung)
                duration: animationDuration,
                delay: delay,
                onUpdate: drawCards,
              })
              .to(card, {
                currentX: centerX, // Di chuyển vào giữa
                duration: animationDuration,
                onUpdate: drawCards,
              })
              .to(card, {
                currentY: card.y, // Quay về vị trí gốc theo Y
                currentX: card.x, // Quay về vị trí gốc theo X
                duration: animationDuration,
                onUpdate: drawCards,
                onComplete: () => {
                  // Khi hoàn thành hiệu ứng xào bài
                  if (index === cards.length - 1) {
                    returnToOriginalPositions(); // Gọi hàm đưa về vị trí ban đầu
                  }
                },
              });
          });
          FlipLoop();
        }
        function returnToOriginalPositions() {
          // Giai đoạn 3: Trả thẻ về vị trí ban đầu
          gsap.to(cards, {
            currentX: (i) => cards[i].x,
            currentY: (i) => cards[i].y,
            duration: 1,
            onUpdate: drawCards,
            onComplete: () => {
              canClickCards = true; // Cho phép click mở thẻ sau khi hoàn tất
            },
          });
        }
        function revealAndHideCards() {
          const flipDuration = 0.5; // Thời gian lật từng thẻ
          const delayBetweenFlips = 0.2; // Khoảng thời gian giữa các lần lật
          const totalRevealTime = 3000; // Thời gian giữ thẻ ở trạng thái lật (3 giây)
          setTimeout(() => {
            // Lật tất cả các thẻ lần lượt để hiển thị mặt trước
            cards.forEach((card, index) => {
              setTimeout(() => {
                gsap.to(card, {
                  rotationY: 90, // Lật đến 90 độ
                  duration: flipDuration / 2,
                  onUpdate: drawCards, // Vẽ lại trong khi lật
                  onComplete: () => {
                    card.flipped = true; // Cập nhật trạng thái lật
                    drawCards(); // Vẽ lại thẻ đã lật
                    gsap.to(card, {
                      rotationY: 0, // Hoàn thành lật mặt trước
                      duration: flipDuration / 2,
                      onUpdate: drawCards,
                      onComplete: () => {
                        // flipSound.play()
                      },
                    });
                  },
                });
              }, index * delayBetweenFlips * 1000); // Thêm delay giữa các thẻ
            });
            // Đợi tổng thời gian lật + thời gian giữ mặt trước
            setTimeout(() => {
              // Lật tất cả các thẻ trở lại lần lượt
              cards.forEach((card, index) => {
                setTimeout(() => {
                  gsap.to(card, {
                    rotationY: 90, // Lật đến 90 độ
                    duration: flipDuration / 2,
                    onUpdate: drawCards, // Vẽ lại trong khi lật
                    onComplete: () => {
                      card.flipped = false; // Cập nhật trạng thái úp
                      drawCards(); // Vẽ lại thẻ đã úp
                      gsap.to(card, {
                        rotationY: 0, // Hoàn thành lật mặt sau
                        duration: flipDuration / 2,
                        onUpdate: drawCards,
                        onComplete: () => {
                          if (index === cards.length - 1) {
                            animateCards(); // Bắt đầu xáo trộn khi hoàn thành
                          }
                        },
                      });
                    },
                  });
                }, index * delayBetweenFlips * 1000); // Thêm delay giữa các thẻ
              });
            }, cards.length * delayBetweenFlips * 1000 + totalRevealTime);
            const audio = new Audio(
              lucky_money_ajax_url.lucky_money_dir_url +
                "/assets/images/flipcard.mp3"
            );
            playAndPause(audio, 2200, () => {
              // Phát 1 giây
              setTimeout(() => {
                // Dừng một khoảng thời gian
                playAndPause(audio, 2000, () => {
                  // Phát lại 1 giây
                  // console.log("Hoàn tất phát âm thanh."); // Hoàn tất
                });
              }, 2500); // Khoảng thời gian dừng giữa hai lần phát
            });
          }, 2000);
        }
        // Hàm mở popup với hiệu ứng zoom-in
        function openPopup(reward) {
          // console.log(prizeType);
          let prefix = null;
          let noti = null;
          if (prizeType == "none") {
            prefix = lucky_money_ajax_url.lucky_money_failed_prefix;
            noti = lucky_money_ajax_url.lucky_money_failed;
            reciveBtn.style.display = "none";
          } else {
            prefix =
              lucky_money_ajax_url.lucky_money_success_prefix +
              " " +
              reward.text;
            noti = lucky_money_ajax_url.lucky_money_success;
          }
          const popup = document.getElementById("popup");
          const popupContent = document.querySelector(".popups-content");
          // Hiển thị thông tin phần thưởng trong popup
          document.getElementById("popupNoti").innerHTML = `${noti}`;
          document.getElementById("popupMessage").innerHTML = `
        <p class="text-wish">${prefix}</p>
        <img src="${reward.image}"  style="width: 100%; border-radius: 8px;"/>
      `;

          popup.style.display = "flex";
          popupContent.style.transform = "scale(0)";
          setTimeout(() => {
            popupContent.style.transform = "scale(1)";
            animateConfetti();
            winSound.play();
          }, 50);
          if (prizeType === "none") {
            setTimeout(() => {
              window.location.reload();
            }, 3000);
          }
        }
        function closePopup() {
          document.getElementById("popup").style.display = "none";
        }
        // let canClickCards = false; // Mặc định không cho phép click
        canvas.addEventListener("click", (e) => {
          // Kiểm tra nếu không được phép click hoặc một thẻ đang được xử lý
          if (!canClickCards || isCardOpened) return;
          flipSound.play();
          const mouseX = e.offsetX;
          const mouseY = e.offsetY;
          cards.forEach((card) => {
            if (
              mouseX >= card.currentX &&
              mouseX <= card.currentX + card.width &&
              mouseY >= card.currentY &&
              mouseY <= card.currentY + card.height
            ) {
              if (card.flipped) return; // Bỏ qua thẻ đã lật
              isCardOpened = true; // Đặt trạng thái: thẻ đang được xử lý
              // Gọi hiệu ứng lật thẻ
              gsap.to(card, {
                rotationY: 90, // Lật đến 90 độ
                duration: 0.5, // Thời gian lật
                onUpdate: drawCards, // Vẽ lại trong khi lật
                onComplete: () => {
                  card.flipped = true; // Đánh dấu trạng thái thẻ đã lật
                  // Gán phần thưởng cố định (nếu cần)
                  card.reward = {
                    ...popupReward[0],
                    imageElement: new Image(),
                  };
                  card.reward.imageElement.src = popupReward[0].image;
                  // Vẽ lại thẻ đã lật
                  drawCards();
                  // Lật tiếp mặt trước
                  gsap.to(card, {
                    rotationY: 0, // Lật về vị trí 0 độ
                    duration: 0.5,
                    onUpdate: drawCards,
                    onComplete: async () => {
                      // Hiển thị popup sau khi hoàn thành lật thẻ

                      openPopup(card.reward);

                      if (!spinning) {
                        const { getPrizeToWin, getPrizeToWinAjax2 } =
                          await import("../../assets/api/prizeModule.js");
                        prizeToWin = await getPrizeToWinAjax2(
                          segments,
                          pageEventId,
                          prizeId
                        );
                      } else {
                        const { getPrizeToWinAjax4 } = await import(
                          "../../assets/api/prizeModule.js"
                        );
                        prizeToWin = await getPrizeToWinAjax4(
                          segments,
                          pageEventId,
                          userData,
                          prizeId
                        );
                        // if (result) await sendDatafromAjax(result);
                      }
                      // Đặt lại trạng thái cho phép mở thẻ mới
                      setTimeout(() => {
                        // isCardOpened = false;
                      }, 500); // Thêm thời gian nhỏ để chắc chắn popup đã hiển thị
                    },
                  });
                },
              });
            }
          });
        });
        // window.addEventListener("resize", resizeCanvas);
        const btnStarts = document.querySelectorAll(".gameStart");
        if (btnStarts) {
          btnStarts.forEach((btnStart) => {
            btnStart.addEventListener("click", () => {
              if (!spinning) resizeCanvas();
            });
          });
        }
        const reciveBtn = document.querySelector(".reciveBtn");
        if (reciveBtn) {
          reciveBtn.addEventListener("click", () => {
            if (!spinning) window.location.reload();
            else {
              closePopup();
              mwFormUser.remove();
              document.getElementById("lm_game").classList.remove("open");
              // Select all elements with the class "notiSuccess"
              let elements = document.querySelectorAll(".d-none");

              // Loop through all the elements and apply the style
              elements.forEach(function (element) {
                element.style.display = "block";
              });
              document.getElementById("lm_form").classList.add("open");
            }
          });
        }
        // ========================================
        // Danh sách URL của ảnh decor
        const decorImages = [
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/dcor1.png",
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/dcor2.png",
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/dcor3.png",
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/dcor4.png",
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/dcor5.png",
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/dcor6.png",
        ];
        // Truy cập boxDrop
        const boxDrops = document.querySelectorAll(".boxDropFrame");
        if (boxDrops) {
          boxDrops.forEach((boxDrop) => {
            // Hàm tạo item decor
            function createDecorItem(imageSrc) {
              const decorItem = document.createElement("img");
              decorItem.src = imageSrc;
              decorItem.className = "decor-item";
              // Thiết lập kích thước ngẫu nhiên
              const size = Math.random() * 50 + 2; // Từ 30px đến 80px
              decorItem.style.width = `${size}px`;
              // Thiết lập vị trí ngang ngẫu nhiên
              const startX = Math.random() * window.innerWidth; // Random từ 0 đến chiều rộng màn hình
              decorItem.style.left = `${startX}px`;
              // Thiết lập thời gian và tốc độ rơi ngẫu nhiên
              const duration = Math.random() * 3 + 5; // Thời gian rơi từ 2 đến 5 giây
              decorItem.style.animationDuration = `${duration}s`;
              // Thêm vào boxDrop
              boxDrop.appendChild(decorItem);
              // Xóa item khi kết thúc animation
              setTimeout(() => {
                decorItem.remove();
              }, duration * 1000); // Xóa sau khi hoàn thành animation
            }
            // Tạo nhiều item cùng lúc để tăng mật độ rơi
            function createMultipleItems() {
              const itemsToCreate = Math.floor(Math.random() * 2) + 2; // Tạo ngẫu nhiên 2-4 item mỗi lần
              for (let i = 0; i < itemsToCreate; i++) {
                const randomImage =
                  decorImages[Math.floor(Math.random() * decorImages.length)];
                const decorItem = createDecorItem(randomImage);
              }
            }
            // Render và tạo hiệu ứng rơi liên tục với mật độ cao
            setInterval(() => {
              createMultipleItems();
            }, 300); // Giảm thời gian giữa các lần tạo xuống 300ms
          });
        }
        // =========================Confetti
        const confettiCanvas = document.getElementById("confetti");
        const ctxs = confettiCanvas.getContext("2d");
        // Configuration du canvas pour occuper tout l'écran
        confettiCanvas.width = window.innerWidth;
        confettiCanvas.height = window.innerHeight;
        // Paramètres des confettis
        const confettis = [];
        const colors = ["#FF007A", "#7A00FF", "#00FF7A", "#FFD700", "#00D4FF"];
        // Fonction pour générer un confetti
        function createConfetti() {
          const confetti = {
            x: Math.random() * confettiCanvas.width,
            y: Math.random() * confettiCanvas.height - confettiCanvas.height,
            size: Math.random() * 10 + 5,
            color: colors[Math.floor(Math.random() * colors.length)],
            speedX: Math.random() * 3 - 1.5,
            speedY: Math.random() * 5 + 2,
            rotation: Math.random() * 360,
          };
          confettis.push(confetti);
        }
        // Générer des confettis une fois
        for (let i = 0; i < 200; i++) {
          createConfetti();
        }
        // Fonction pour animer les confettis
        function animateConfetti() {
          ctxs.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
          confettis.forEach((confetti, index) => {
            confetti.x += confetti.speedX;
            confetti.y += confetti.speedY;
            confetti.rotation += confetti.speedX;
            // Dessiner le confetti
            ctxs.save();
            ctxs.translate(confetti.x, confetti.y);
            ctxs.rotate((confetti.rotation * Math.PI) / 180);
            ctxs.fillStyle = confetti.color;
            ctxs.fillRect(
              -confetti.size / 2,
              -confetti.size / 2,
              confetti.size,
              confetti.size
            );
            ctxs.restore();
            // Supprimer les confettis qui sortent de l'écran
            if (confetti.y > confettiCanvas.height) {
              confettis.splice(index, 1);
            }
          });
          if (confettis.length > 0) {
            requestAnimationFrame(animateConfetti);
          }
        }
        // ================= Click Sound
        const clickSound = new Audio(
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/click.mp3"
        );
        const winSound = new Audio(
          lucky_money_ajax_url.lucky_money_dir_url + "/assets/images/win.mp3"
        );
        const flipSound = new Audio(
          lucky_money_ajax_url.lucky_money_dir_url +
            "/assets/images/flipcard.mp3"
        );
        const clSounds = document.querySelectorAll(".clSound");
        if (clSounds) {
          clSounds.forEach((clSound) => {
            clSound.addEventListener("click", () => {
              clickSound.play();
            });
          });
        }
        // ================= Flip Loop
        function FlipLoop() {
          const audio = new Audio(
            lucky_money_ajax_url.lucky_money_dir_url +
              "/assets/images/flipcard.mp3"
          );
          audio.loop = true; // Kích hoạt lặp lại liên tục
          // Bắt đầu phát
          audio.playbackRate = 2.0;
          audio.play();
          setTimeout(() => {
            audio.pause();
            audio.currentTime = 0; // Đặt lại thời gian phát về 0
          }, 2200);
        }
        // Hàm phát âm thanh trong 1 giây rồi dừng
        function playAndPause(audios, duration, callback) {
          audios.loop = true; // Kích hoạt lặp lại liên tục
          // Bắt đầu phát
          audios.playbackRate = 2.0;
          audios.play(); // Phát âm thanh
          setTimeout(() => {
            audios.pause(); // Dừng âm thanh
            audios.currentTime = 0; // Đặt lại thời gian phát về 0
            if (callback) callback(); // Gọi callback nếu có
          }, duration);
        }
      }
    }
  }
}
