// URLからspot_idを取得
const urlParams = new URLSearchParams(window.location.search);
const spot_id = urlParams.get('id');
console.log('受け取った値:', spot_id);

// DOM読み取り
const detail = document.querySelector('.detail');
const imgpass = document.querySelector('#imgpass');

// PHPに送るデータをセット
const postdata = { imgid: spot_id, branch: 1 };
fetch('http://localhost/geocation/php/spot_detailAPI.php', { // 第1引数に送り先
  method: 'POST', // メソッド指定
  headers: { 'Content-Type': 'application/json' }, // jsonを指定
  body: JSON.stringify(postdata)
})
  .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
  .then(res => {
    console.log(res);
    console.log(imgpass);
    imgpass.src = `../uploads/${res[0]['PHOTO']}`;
    // HTMLを生成
    detail.insertAdjacentHTML('afterbegin', 
    `<h2>${res[0]['SPOTNAME']}</h2>
    <p>満足度: <span>${res[0]['STERGOOD']}</span></p>  
    <p>登録者: <span>${res[0]['NAME']}</span></p>
    <p>費用: <span>${res[0]['COST']}</span></p>
    <p>住所: <span>${res[0]['ADDRESS']}</span></p>
    <div class="note">
      <p>備考欄</p>
      <p id="comment">${res[0]['REMARKS']}</p>
    </div>
    `);
  })
  .catch(error => {
    console.log(error); // エラー表示
  });

window.onload = function() {
  document.querySelector('.diary-view-btn').addEventListener('click', (event) => {
    // 現在のスポットのIDを取得
    const spotId = new URLSearchParams(window.location.search).get('id');

    // PHPスクリプトを呼び出して日記のタイトルを取得
    fetch('http://localhost/geocation/php/spot_detailAPI.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ imgid: spotId })
    })
    .then(response => response.json())
    .then(res => {
      // 日記のタイトルを取得
      const spotName = res[0]['SPOTNAME'];
      
      // ランキングページにリダイレクトし、タイトルをURLパラメータとして渡す
      window.location.href = `ranking.php?SPOTNAME=${encodeURIComponent(spotName)}&TYPE=2`;
    })
    .catch(error => {
      console.log(error); // エラー表示
    });
  });
  
  document.querySelector('.diary-post-btn').addEventListener('click', () => {
    const spotId = new URLSearchParams(window.location.search).get('id');
    if (spotId) {
      window.location.href = `diary_post.php?spot_id=${spotId}`;
    } else {
      alert('スポットIDが見つかりません。');
    }
  });
}
