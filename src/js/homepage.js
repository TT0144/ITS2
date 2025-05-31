//使い方
const spot_reco = document.querySelector("#spot-reco");
const spot_rank = document.querySelector("#spot-rank");
const diary_rank = document.querySelector("#diary-rank");

//homepage表示分岐変数
const homepage = { branch: 0, sort_text: "", radiovalue: "" };
console.log(homepage);
//おすすめ欄API
fetch("../API.php", {
  // 第1引数に送り先
  method: "POST", // メソッド指定
  headers: { "Content-Type": "application/json" }, // jsonを指定
  body: JSON.stringify(homepage),
})
  .then((response) => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
  .then((res) => {
    console.log(res);
    if (res[0].hasOwnProperty("RANDOM")) {
      res[0]["RANDOM"].forEach((element) => {
        if (!element["PHOTO"]) {
          element["PHOTO"] = "../img/noimage.png";
        }
        spotitem(
          spot_reco,
          element["PHOTO"],
          element["SPOTNAME"],
          element["SPOTNAME"],
          element["SPOT_ID"]
        );
      });
    }
    if (res[0].hasOwnProperty("SPOT")) {
      res[0]["SPOT"].forEach((element) => {
        if (!element["PHOTO"]) {
          element["PHOTO"] = "../img/noimage.png";
        }
        spotitem(
          spot_rank,
          element["PHOTO"],
          element["SPOTNAME"],
          element["SPOTNAME"],
          element["SPOT_ID"]
        );
      });
    }
    if (res[0].hasOwnProperty("DIARY")) {
      res[0]["DIARY"].forEach((element) => {
        if (!element["PHOTO"]) {
          element["PHOTO"] = "../img/noimage.png";
        }
        diaryitem(
          diary_rank,
          element["PHOTO"],
          element["TITLE"],
          element["TITLE"],
          element["DIARY_ID"]
        );
      });
    }
  })
  .catch((error) => {
    console.log(error); // エラー表示
  });

//要素を入れて表示　第一：親要素　二：画像そのもの　三：画像説明（最悪なくてもよし）　四：スポットの名前またはタイトル
function spotitem(element, img_url, img_name, title_name, spot_id) {
  let rank_num = element.querySelectorAll(".spot").length;
  console.log(rank_num);
  rank_num = -rank_num + 5;
  //おすすめはランキング表示をしない
  if (element == document.querySelector("#spot-reco")) {
    element.insertAdjacentHTML(
      "afterbegin",
      `<div class="spot">
          <img src=./uploads/${img_url} alt=${img_name} id=${spot_id}>
          <h2>${title_name}</h2>
      </div>
      `
    );
  } else {
    //ランキングは表示を追加
    element.insertAdjacentHTML(
      "afterbegin",
      `<div class= "rank_box">
      <div class="rank_text rank_num${rank_num}"><img src="../img/3370-removebg-preview.png" alt="王冠 ロゴ"height=20px width=20px>${rank_num}</div>
      <div class="spot">
        <img src=./uploads/${img_url} alt=${img_name} id=${spot_id}>
        <h2>${title_name}</h2>
      </div>
    </div>`
    );
  }
}

//要素を入れて表示　第一：親要素　二：画像そのもの　三：画像説明（最悪なくてもよし）　四：スポットの名前またはタイトル
function diaryitem(element, img_url, img_name, title_name, diary_id) {
  let rank_num = element.querySelectorAll(".diary").length;
  console.log(rank_num);
  rank_num = -rank_num + 5;
  element.insertAdjacentHTML(
    "afterbegin",
    `<div class= "rank_box">
  
    <div class="rank_text rank_num${rank_num}"><img src="../img/3370-removebg-preview.png" alt="王冠 ロゴ"height=20px width=20px>${rank_num}</div>
    <div class="diary">
      <img src=./uploads/${img_url} alt=${img_name} id=${diary_id}>
      <h2>${title_name}</h2>
    </div>
  </div>`
  );
}

document.querySelector("#section-item").addEventListener("click", (event) => {
  if (event.target.closest(".spot")) {
    const parentitemid = event.target
      .closest(".spot")
      .firstElementChild.getAttribute("id");
    window.location.href = `../spot_detail.php?id=${parentitemid}`;
  } else {
    const parentitemid = event.target
      .closest(".diary")
      .firstElementChild.getAttribute("id");
    window.location.href = `../diary_detail.php?id=${parentitemid}`;
  }
});

//もっと見るボタン処理ーーーーーーーーーーーーーーーーーーーーーーーーーーーー
document.querySelector("#more_spotrank").addEventListener("click", (event) => {
  window.location.href = `ranking.php?id=1`;
});

document.querySelector("#more_diaryrank").addEventListener("click", (event) => {
  window.location.href = `ranking.php?id=2`;
});

//もっと見るボタン処理ーーーーーーーーーーーーーーーーーーーーーーーーーーーー

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

function toggleProfileDropdown() {
  const dropdown = document.getElementById("profile-dropdown");
  if (dropdown.style.display === "none" || dropdown.style.display === "") {
    dropdown.style.display = "block";
  } else {
    dropdown.style.display = "none";
  }
}
