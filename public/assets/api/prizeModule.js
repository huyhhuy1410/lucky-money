import { ToastModule } from "../../js/module/ToastModule.js";
export async function getPrizeToWin(segments) {
  return fetch("../plugins/lucky_money/assets/api/win.json")
    .then((response) => response.json())
    .then((data) => {
      const winId = data.win[0].id;
      const prizeToWin = segments.find((segment) => segment.id === winId);
      console.log("Prize to win:", prizeToWin);
      if (prizeToWin) {
        return prizeToWin;
      } else {
        console.error(`Prize with id ${winId} not found in segments.`);
        return null;
      }
    })
    .catch((error) => {
      console.error("Error fetching prize data:", error);
      return null;
    });
}
export async function getPrizeToWinAjax4(
  segments,
  page_id,
  user_data,
  prizeId
) {
  try {
    const formData = new FormData();
    formData.append("page_id", page_id);
    formData.append("user_data", JSON.stringify(user_data));
    formData.append("lm_prize_id", prizeId);
    formData.append("action", "lm_ajax_program_prize_win_4");

    const formDataParams = new URLSearchParams(formData);

    // Add loading class to the submit button
    // submitButton.classList.add("loading");

    const response = await fetch(lucky_money_ajax_url.ajaxURL, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: formDataParams,
    });

    const results = await response.json();

    // Remove loading class from the submit button

    if (results.success) {
      // console.log(prized);
      const winId = results.data.prize_id;

      const prizeToWin = segments.find((segment) => segment.id === winId);
      if (prizeToWin) {
        return prizeToWin; // Return both prizeToWin and the results JSON
      }
    } else {
      ToastModule({
        type: "error", 
        content: results.data.message,

      });
      return null; // Return null for prizeToWin and include results JSON
    }
  } catch (error) {
    // Remove loading class in case of error

    console.error("Error fetching prize data:", error);
    return null;
  }
}
export async function getPrizeToWinAjax(page_id) {
  try {
    const formElement = document.getElementById(
      "formLucky_MoneyUserInformation"
    );
    const submitButton = formElement.querySelector('button[type="submit"]');
    const formData = new FormData(formElement);
    formData.append("page_id", page_id);
    formData.append("action", "lm_ajax_program_prize_win");

    const formDataParams = new URLSearchParams(formData);

    // Add loading class to the submit button
    submitButton.classList.add("loading");

    const response = await fetch(lucky_money_ajax_url.ajaxURL, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: formDataParams,
    });

    const results = await response.json();

    // Remove loading class from the submit button
    submitButton.classList.remove("loading");

    if (results.success) {
      const userData = results.data;
      // const prized = results.data;
      // // console.log(prized);
      // const winId = results.data.prize_id;
      // const winNextId = results.data.prize_encode_next;
      // if (winNextId != undefined) {
      //   const hiddenInput = formElement.querySelector(
      //     'input[name="lm_prize_id"]'
      //   );
      //   if (hiddenInput) {
      //     hiddenInput.value = winNextId;
      //   }
      // }

      // const prizeToWin = segments.find((segment) => segment.id === winId);
      // if (prizeToWin) {
      // Reset the form after successful AJAX call
      formElement.reset();
      return userData; // Return both prizeToWin and the results JSON
      // }
    } else {
      ToastModule({
        type: "error", 
        content: results.data.message,

      });
      return null; // Return null for prizeToWin and include results JSON
    }
  } catch (error) {
    // Remove loading class in case of error
    const formElement = document.getElementById(
      "formLucky_MoneyUserInformation"
    );
    const submitButton = formElement.querySelector('button[type="submit"]');
    submitButton.classList.remove("loading");
    console.error("Error fetching prize data:", error);
    return null;
  }
}

export function prioritizePrizeSegment(segments, prizeToWin) {
  const prizeIndex = segments.findIndex(
    (segment) => segment.id === prizeToWin.id
  );
  if (prizeIndex !== -1 && prizeIndex !== 0) {
    const prizeSegment = segments.splice(prizeIndex, 1)[0];
    const segmentsBeforePrize = segments.splice(0, prizeIndex);
    segments.push(...segmentsBeforePrize);
    segments.unshift(prizeSegment);
  }
}

export async function getPrizeToWinAjax2(segments, page_id, prize_id) {
  try {
    const lmSpinFirst = document.getElementById("gameCanvas");
    const formElement = document.getElementById(
      "formLucky_MoneyUserInformation2"
    );
    const formData = new FormData();
    formData.append("page_id", page_id);
    formData.append("prize_id", prize_id);
    formData.append("action", "lm_ajax_program_prize_win_2");

    const formDataParams = new URLSearchParams(formData);

    lmSpinFirst.classList.add("loading");

    const response = await fetch(lucky_money_ajax_url.ajaxURL, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: formDataParams,
    });

    const results = await response.json();

    lmSpinFirst.classList.remove("loading");

    if (results.success) {
      const winId = results.data.prize_id;
      const winNextId = results.data.prize_encode_next;

      if (winNextId != undefined) {
        if (lmSpinFirst) {
          lmSpinFirst.dataset.prize_id = winNextId;
        }
      }

      const prizeToWin = segments.find((segment) => segment.id === winId);
      if (prizeToWin) {
        const resultId = results.data.result_id;
        if (formElement) {
          const hiddenInput = formElement.querySelector(
            'input[name="lm_result_id"]'
          );
          if (hiddenInput && resultId != undefined) {
            hiddenInput.value = resultId;
          }
        }
        return prizeToWin;
      }
    } else {
      ToastModule({
        type: "error", 
        content: results.data.message,

      });
      return null;
    }
  } catch (error) {
    // Remove loading class in case of error
    const lmSpinFirst = document.getElementById("gameCanvas");
    // const formElement = document.getElementById(
    //   "formLucky_MoneyUserInformation2"
    // );
    lmSpinFirst.classList.remove("loading");
    console.error("Error fetching prize data:", error);
    return null;
  }
}

export async function updatePrizeTicketInformationAjax(page_id) {
  try {
    const formElement = document.getElementById(
      "formLucky_MoneyUserInformation2"
    );
    const submitButton = formElement.querySelector('button[type="submit"]');
    const formData = new FormData(formElement);
    formData.append("page_id", page_id);
    formData.append("action", "lm_ajax_program_prize_result_updation");

    const formDataParams = new URLSearchParams(formData);

    // Add loading class to the submit button
    submitButton.classList.add("loading");

    const response = await fetch(lucky_money_ajax_url.ajaxURL, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: formDataParams,
    });

    const results = await response.json();

    // Remove loading class from the submit button
    submitButton.classList.remove("loading");
    return results;
  } catch (error) {
    // Remove loading class in case of error
    const formElement = document.getElementById(
      "formLucky_MoneyUserInformation2"
    );
    const submitButton = formElement.querySelector('button[type="submit"]');
    submitButton.classList.remove("loading");
    console.error("Error fetching prize data:", error);
    return null;
  }
}
