document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const diary_id = urlParams.get("id");
  console.log("受け取った値:", diary_id);
  console.log(isLoggedIn);

  // DOM読み取り
  const detail = document.querySelector(".detail");
  const imgpass = document.querySelector("#imgpass");
  const BackButton = document.querySelector("#BackButton");

  // phpに送るデータをセット
  const postdata = { diary_id: diary_id, goodnum: "" };
  console.log(postdata);

  fetch("./diary_detailAPI.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(postdata),
  })
    .then((response) => response.json())
    .then((res) => {
      console.log(res);
      const photosrc = res[0]["PHOTO"]
        ? `./uploads/${res[0]["PHOTO"]}`
        : "./img/noimage.png";
      imgpass.src = photosrc;
      // HTMLを生成
      detail.insertAdjacentHTML(
        "afterbegin",
        `<h2>${res[0]["TITLE"]}</h2>
      <div>投稿者: ${res[0]["NAME"]}</div>
      <div>投稿日時: ${res[0]["CREATED_AT"]}</div>
      <div id="gooditem">
          <img id="goodbtn" src="${
            res[0]["LIKED"]
              ? "../img/goodbtn_black.png"
              : "../img/goodbtn_transparent.png"
          }" alt="ロゴ" width="50">
          <p id="good_count">${res[0]["GOOD"]}</p>
      </div>
      <div id="text">
          <div id="comment">${res[0]["TEXT"]}</div>
      </div>`
      );

      // ログインしている場合、いいね状態をチェックして黒アイコンにする
      if (res[0]["LIKED"]) {
        document.querySelector("#goodbtn").src = "../img/goodbtn_black.png";
      }
    })
    .catch((error) => {
      console.log(error);
    });

  detail.addEventListener("click", (event) => {
    const goodbtn = event.target.closest("#goodbtn");
    if (goodbtn) {
      if (isLoggedIn) {
        if (goodbtn.src.includes("goodbtn_transparent.png")) {
          goodbtn.src = "../img/goodbtn_black.png";
          document.querySelector("#good_count").textContent++;

          const updategood = { diary_id: diary_id, goodnum: 1 };
          fetch("./diary_detailAPI.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(updategood),
          })
            .then((response) => response.json())
            .then((res) => {
              console.log(res);
            })
            .catch((error) => {
              console.log(error);
            });
        } else {
          goodbtn.src = "../img/goodbtn_transparent.png";
          document.querySelector("#good_count").textContent--;

          const updategood = { diary_id: diary_id, goodnum: -1 };
          fetch("./diary_detailAPI.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(updategood),
          })
            .then((response) => response.json())
            .then((res) => {
              console.log(res);
            })
            .catch((error) => {
              console.log(error);
            });
        }
      } else {
        alert("ログインしてください");
      }
    }
  });

  // 前の画面に戻る
  BackButton.addEventListener("click", (event) => {
    window.history.back();
  });
});

if (event.target.closest(".user-icon")) {
  dropdown.style.display = "block";
} else if (
  event.target.closest(".profile-container") == null &&
  event.target.closest(".menu") == null
) {
  dropdown.style.display = "none";
}
document.addEventListener("click", function (event) {
  var dropdown = document.getElementById("profile-dropdown");
  if (event.target.closest(".user-icon")) {
    dropdown.style.display =
      dropdown.style.display === "none" || dropdown.style.display === ""
        ? "block"
        : "none";
  } else if (
    event.target.closest(".profile-container") == null &&
    event.target.closest(".menu") == null
  ) {
    dropdown.style.display = "none";
  }
});
//プルダウンを閉じる
document.addEventListener("click", function (event) {
  var dropdown = document.getElementById("profile-dropdown");
  if (event.target.closest(".user-icon")) {
    dropdown.style.display =
      dropdown.style.display === "none" || dropdown.style.display === ""
        ? "block"
        : "none";
  } else if (
    event.target.closest(".profile-container") == null &&
    event.target.closest(".menu") == null
  ) {
    dropdown.style.display = "none";
  }
});
