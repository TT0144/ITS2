//スポットか日記のもっと見るを取得
const urlParams = new URLSearchParams(window.location.search);
const spotName = urlParams.get("SPOTNAME");
const type = urlParams.get("TYPE");
console.log("受け取った値:", spotName, type);

// URLパラメータからspot_idを取得
const spot_id = urlParams.get("spot_id");
const divide_id = urlParams.get("id");
console.log("受け取った値:", divide_id);

//DOM読み取り
const results = document.querySelector("#results");
const searchboxvalue = document.querySelector("#searchbox");
const radioboxitem = document.querySelector("#radioboxitem");
const listitem = document.querySelector("#listitem");
var baranch_num = 0;

//もっと見るからページを読み込んだ時
if (divide_id == 0 || divide_id == null) {
  //スポット
} else if (divide_id == 1) {
  display_spotrank();
  //日記
} else {
  display_diaryrankk();
}
//スポットランキングボタン処理
document.querySelector("#spot-ranking").addEventListener("click", () => {
  display_spotrank();
});

//日記ランキングボタン処理
document.querySelector("#diary-ranking").addEventListener("click", () => {
  display_diaryrankk();
});

//周辺地域スポット表示処理
document.querySelector("#location_button").addEventListener("click", () => {
  display_location();
});

//ランキング表示処理
function display_spotrank() {
  removeresultitem_spots();
  removeserchbox_items();
  removeradiobotton();
  setradiobotton_order();
  baranch_num = 1;

  setitem(1, "", 2);
}

//日記表示処理
function display_diaryrankk() {
  removeresultitem_spots();
  removeserchbox_items();
  removeradiobotton();
  setradiobotton_order();
  baranch_num = 2;
  createresult_items(2, "", 2);
}

//地域別表示
function display_location() {
  removeresultitem_spots();
  removeserchbox_items();
  removeradiobotton();
  location_order();
  baranch_num = 3;
  setitem(3, "", 5);
}

//抽象化アイテムクリエイト
function createresult_items(branch, sort_text, radiovalue) {
  setitem(branch, sort_text, radiovalue);
}
//検索項目時処理
document.querySelector("#search-button").addEventListener("click", () => {
  removeresultitem_spots();
  removeserchbox_items();
  removeradiobotton();
  serchbox_items();
});

//ラジオボタン削除
function removeradiobotton() {
  while (radioboxitem.firstChild) {
    radioboxitem.removeChild(radioboxitem.firstChild);
  }
}

//検索ボックス表示
function serchbox_items() {
  listitem.insertAdjacentHTML(
    "afterbegin",
    `<div id="ranking-select">
        <select name="sort" id="selectsort">
            <option value="0">すべて</option>
            <option value="1">スポット</option>
            <option value="2">日記</option>
        </select>
        <input type="text" id="searchbox" placeholder="検索キーワードを入力"value = "">
        <div id="searchicon">
          <img src="../img/虫眼鏡の無料アイコン8.png" alt="虫眼鏡アイコン" width="20" height="21">
        </div>
    </div>`
  );
}

//検索ボックス削除
function removeserchbox_items() {
  const ranking_select = document.querySelector("#ranking-select");
  if (ranking_select) {
    ranking_select.remove();
  }
}

//昇順降順ラジオボタン表示
function setradiobotton_order() {
  radioboxitem.insertAdjacentHTML(
    "afterbegin",
    `<input type="radio" name="radiobotton" value="1">昇順
    <input type="radio" name="radiobotton" value="2"checked="checked">降順`
  );
}

//更新日時変更ラジオボタン表示
function setradiobotton_update() {
  radioboxitem.insertAdjacentHTML(
    "afterbegin",
    `<input type="radio" name="radiobotton" value="3"checked="checked">新しい順
    <input type="radio" name="radiobotton" value="4">古い順`
  );
}

//地域別ラジオボタン表示
function location_order() {
  radioboxitem.insertAdjacentHTML(
    "afterbegin",
    `<input type="radio" name="radiobotton" value="1">北海道
    <input type="radio" name="radiobotton" value="2">東北
    <input type="radio" name="radiobotton" value="5" checked="checked">関東
    <input type="radio" name="radiobotton" value="6">東海
    <input type="radio" name="radiobotton" value="7">北陸
    <input type="radio" name="radiobotton" value="8">近畿
    <input type="radio" name="radiobotton" value="9">中国
    <input type="radio" name="radiobotton" value="10">四国
    <input type="radio" name="radiobotton" value="11">九州
    <input type="radio" name="radiobotton" value="12">沖縄
    `
  );
}

//ラジオボタン読み取り
function checkradiobotton() {
  if (document.radiobottonitem.radiobotton.value) {
    return document.radiobottonitem.radiobotton.value;
  }
}

//結果表示を消す
function removeresultitem_spots() {
  //既にある要素を削除
  while (results.firstChild) {
    results.removeChild(results.firstChild);
  }
}
// URLパラメータからタイトルを取得し、検索ボックスに設定
window.onload = function () {
  if (spotName) {
    searchbox.value = spotName;
    document.querySelector("#selectsort").value = type || "0"; // デフォルトは全てのカテゴリ
    setitem(type || "0", spotName, 3); // デフォルトで最新順にソート
    setradiobotton_update();
  }
};

//APIにデータを送って表示
//以下引数(第一：日記スポットかあるいはどっちも、第二：検索文字、第三：ラジオボタンの入力（value))
function setitem(resbranch, sort_text, radiovalue) {
  const branchdata = {
    branch: resbranch,
    sort_text: sort_text,
    radiovalue: radiovalue,
  };
  console.log(branchdata);
  fetch("../API.php", {
    // 第1引数に送り先
    method: "POST", // メソッド指定
    headers: { "Content-Type": "application/json" }, // jsonを指定
    body: JSON.stringify(branchdata),
  })
    .then((response) => response.json()) // 返ってきたレスポンスをjsonで受 け取って次のthenへ渡す
    .then((res) => {
      console.log(res);
      console.log(res[0].hasOwnProperty("SPOT"));
      console.log(res[0].hasOwnProperty("DIARY"));

      if (resbranch == 0 || resbranch == 1 || resbranch == 3) {
        if (res[0].hasOwnProperty("SPOT")) {
          res[0]["SPOT"].forEach((element) => {
            resultitem_spot(
              results,
              element["PHOTO"],
              element["SPOTNAME"],
              element["SPOTNAME"],
              element["SPOT_ID"],
              element["REMARKS"]
            );
          });
        }
      }

      if (resbranch == 0 || resbranch == 2) {
        if (res[0].hasOwnProperty("DIARY")) {
          res[0]["DIARY"].forEach((element) => {
            if (!element["PHOTO"]) {
              element["PHOTO"] = "../img/noimage.png";
            }
            resultitem_diary(
              results,
              element["PHOTO"],
              element["TITLE"],
              element["TITLE"],
              element["DIARY_ID"],
              element["TEXT"]
            );
          });
        }
      }
    })
    .catch((error) => {
      console.log(error); // エラー表示
    });
}
//ソート結果を表示(スポット)
function resultitem_spot(
  element,
  img_url,
  img_name,
  title_name,
  spot_id,
  item_text
) {
  element.insertAdjacentHTML(
    "afterbegin",
    `<div class="resultitem spot">
    <div class="itemimg">
        <img src=../uploads/${img_url} alt=${img_name} id=${spot_id} class = photoimg width="200" height="210">
    </div>
    <div class="item-explan">
        <div class="list-title">${title_name}</div>
        <div class="item-text">${item_text}</div>
    </div>
  </div>`
  );
}
//ソート結果を表示(スポット)
function resultitem_diary(
  element,
  img_url,
  img_name,
  title_name,
  spot_id,
  item_text
) {
  element.insertAdjacentHTML(
    "afterbegin",
    `<div class="resultitem diary">
    <div class="itemimg">
        <img src=../uploads/${img_url} alt=${img_name} id=${spot_id} class = photoimg width="200" height="210">
    </div>
    <div class="item-explan">
        <div class="list-title">${title_name}</div>
        <div class="item-text">${item_text}</div>
    </div>
  </div>`
  );
}

//ラジオボタン・検索機能
listitem.addEventListener("click", (event) => {
  if (event.target.closest("#searchicon")) {
    removeresultitem_spots();
    //ラジオボタン、入力、プルダウンの読み取り
    // const radiovalue = checkradiobotton();
    const searchboxvalue = document.querySelector("#searchbox").value;
    const pulldownvalue = document.querySelector("#selectsort").value;
    if (!searchboxvalue == "") {
      setitem(pulldownvalue, searchboxvalue, 3);
      removeradiobotton();
      setradiobotton_update();
    } else {
      //要素削除
      removeresultitem_spots();
    }
  }
  //ラジオボタン(昇順降順）を変更したとき
  if (event.target.closest('[type="radio"]')) {
    const radiovalue = checkradiobotton();
    if (
      radiovalue == 1 ||
      radiovalue == 2 ||
      radiovalue == 5 ||
      radiovalue == 6 ||
      radiovalue == 7 ||
      radiovalue == 8 ||
      radiovalue == 9 ||
      radiovalue == 10 ||
      radiovalue == 11 ||
      radiovalue == 12
    ) {
      removeresultitem_spots();
      setitem(baranch_num, "", radiovalue);
    }
  }
  //ラジオボタン(昇順降順）を変更したとき
  if (event.target.closest('[type="radio"]')) {
    const radiovalue = checkradiobotton();
    const searchboxvalue = document.querySelector("#searchbox").value;
    const pulldownvalue = document.querySelector("#selectsort").value;

    if (radiovalue == 3 || radiovalue == 4) {
      removeresultitem_spots();
      setitem(pulldownvalue, searchboxvalue, radiovalue);
    }
  }
});

results.addEventListener("click", (event) => {
  if (event.target.closest(".spot")) {
    // console.log(event.target.closest('.spot'));
    const parentitemid = event.target
      .closest(".spot")
      .firstElementChild.firstElementChild.getAttribute("id");
    window.location.href = `../spot_detail.php?id=${parentitemid}`;
  } else {
    const parentitemid = event.target
      .closest(".diary")
      .firstElementChild.firstElementChild.getAttribute("id");
    window.location.href = `../diary_detail.php?id=${parentitemid}`;
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const diaryContent = document.querySelector("#diary-content");

  // PHPに送るデータをセット
  const postdata = { spot_id: spot_id };

  fetch("./fetch_diaries.php", {
    // データを取得するPHPスクリプト
    method: "POST", // メソッド指定
    headers: { "Content-Type": "application/json" }, // jsonを指定
    body: JSON.stringify(postdata),
  })
    .then((response) => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
    .then((res) => {
      if (res[0].hasOwnProperty("DIARY")) {
        res[0]["DIARY"].forEach((element) => {
          if (!element["PHOTO"]) {
            element["PHOTO"] = "../img/noimage.png";
          }
          resultitem_diary(
            results,
            element["PHOTO"],
            element["TITLE"],
            element["TITLE"],
            element["DIARY_ID"],
            element["TEXT"]
          );
        });
      }
    })
    .catch((error) => {
      console.log(error); // エラー表示
      diaryContent.insertAdjacentHTML(
        "beforeend",
        "<p>データの取得に失敗しました。</p>"
      );
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
