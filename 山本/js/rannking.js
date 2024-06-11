const results = document.querySelector('#results');

//スポットランキング
document.querySelector('#spot-ranking').addEventListener('click', () => {
  const spot_rank = 1;
  setitem(spot_rank);
});
document.querySelector('#diary-ranking').addEventListener('click', () => {
  const diary_rank = 2;
  setitem(diary_rank);
});


function setitem(resbranch){
  const branchdata = {branch:resbranch}
  console.log(branchdata);
  fetch('http://localhost/geocation/php/API.php', { // 第1引数に送り先
    method: 'POST', // メソッド指定
    headers: { 'Content-Type': 'application/json' }, // jsonを指定
    body:JSON.stringify(branchdata)
  })
    .then(response => response.json()) // 返ってきたレスポンスをjsonで受 け取って次のthenへ渡す
    .then(res => {
      console.log(res)
      if(resbranch == 1){
        res[0]["SPOT"].forEach(element => {
          resultitem(results,element['PHOTO'],element['SPOTNAME'],element['SPOTNAME'],element["SPOT_ID"],element["REMARKS"])
        });
      }else{
        res[0]["DIARY"].forEach(element => {
          if(!element['PHOTO']){
            element['PHOTO'] = '../img/noimage.png'
            console.log(1);
          }
          console.log(element['PHOTO']);
          resultitem(results,element['PHOTO'],element['TITLE'],element['TITLE'],element["DIARY_ID"],element["TEXT"])
        });    
      }
    })
.catch(error => {
    console.log(error); // エラー表示
});
  
}

function resultitem(element,img_url,img_name,title_name,spot_id,item_text){
  element.insertAdjacentHTML('afterbegin', 
  `<div class="resultitem">
    <div class="itemimg">
        <img src=../uploads/${img_url} alt=${img_name} id=${spot_id}"width="200" height="210">
    </div>
    <div class="item-explan">
        <div class="list-title">${title_name}</div>
        <div>${item_text}</div>
    </div>
  </div>`);
}
