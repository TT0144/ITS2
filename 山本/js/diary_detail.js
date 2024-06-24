const urlParams = new URLSearchParams(window.location.search);
const diary_id = urlParams.get('id');
console.log('受け取った値:',diary_id );

//DOM読み取り
const detail = document.querySelector('.detail');
const imgpass = document.querySelector('#imgpass');
const BackButton = document.querySelector('#BackButton');

//
var goodnum = 0;

//phpに送るデータをセット
const postdata = {diary_id:diary_id,goodnum:""}
console.log(postdata);

fetch('http://localhost/geocation/php/diary_detailAPI.php', { // 第1引数に送り先
  method: 'POST', // メソッド指定
  headers: { 'Content-Type': 'application/json' }, // jsonを指定
  body: JSON.stringify(postdata)
})
  .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
  .then(res => {
    console.log(res);
    console.log(imgpass)
    imgpass.src = `../uploads/${res[0]['PHOTO']}`;
    //HTMLを生成
    //ユーザーネームはユーザーテーブルから
    detail.insertAdjacentHTML('afterbegin', 
    `<h2>${res[0]['TITLE']}</h2>
    <div>投稿者: ${res[0]['NAME']}</div>
    <div>投稿日時: ${res[0]['CREATED_AT']}</div>
    <div id="gooditem">
      <img id = "goodbtn"src="../img/goodbtn_transparent.png" alt="ロゴ" width="50">
      <p id = good_count>${res[0]['GOOD']}</p>
    </div>
    <div id="text">
        <div id="comment">${res[0]['TEXT']}</div>
    </div>`);
  })
  .catch(error => {
    console.log(error); // エラー表示
  });

detail.addEventListener('click', (event) => {
  if(event.target.closest('#goodbtn').src == "http://localhost/geocation/img/goodbtn_transparent.png"){
    event.target.closest('#goodbtn').src = "../img/goodbtn_black.png"
    goodnum ++;
    document.querySelector('#good_count').innerHTML++;
  }else{
    event.target.closest('#goodbtn').src = "../img/goodbtn_transparent.png"
    goodnum --;
    document.querySelector('#good_count').innerHTML--;
  }
});
  
//前の画面に戻る
BackButton.addEventListener('click', (event) => {
  window.history.back();
});

//ウィンドウを閉じるとき
window.addEventListener('beforeunload', function (e) {
  const updategood = {diary_id:diary_id,goodnum:goodnum};
  console.log(updategood);
  fetch('http://localhost/geocation/php/diary_detailAPI.php', { // 第1引数に送り先
    method: 'POST', // メソッド指定
    headers: { 'Content-Type': 'application/json' }, // jsonを指定
    body: JSON.stringify(updategood)
  })
    .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
    .then(res => {
    })
    .catch(error => {
      console.log(error); // エラー表示
    });
    // e.preventDefault();  // 画面遷移を無効化

});
