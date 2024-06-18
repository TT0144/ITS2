//使い方
const spot_reco = document.querySelector('#spot-reco');
const spot_rank = document.querySelector('#spot-rank');
const diary_rank = document.querySelector('#diary-rank');

//homepage表示分岐変数
const homepage = {branch:0,sort_text:"",radiovalue:""};

//おすすめ欄API
fetch('http://localhost/geocation/php/API.php', { // 第1引数に送り先
  method: 'POST', // メソッド指定
  headers: { 'Content-Type': 'application/json' }, // jsonを指定
  body:JSON.stringify(homepage)
})
  .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
  .then(res => {
    console.log(res);
    res[0]["SPOT"].forEach(element => {
      spotitem(spot_reco,element['PHOTO'],element['SPOTNAME'],element['SPOTNAME'],element["SPOT_ID"])
    });
    res[0]["SPOT"].forEach(element => {
      spotitem(spot_rank,element['PHOTO'],element['SPOTNAME'],element['SPOTNAME'],element["SPOT_ID"])
    });
    res[0]["DIARY"].forEach(element => {
      if(!element['PHOTO']){
        element['PHOTO'] = '../img/noimage.png'
        console.log(1);
      }
      console.log(element['PHOTO']);
      diaryitem(diary_rank,element['PHOTO'],element['TITLE'],element['TITLE'],element["DIARY_ID"])
    });

  })
  .catch(error => {
    console.log(error); // エラー表示
  });


//要素を入れて表示　第一：親要素　二：画像そのもの　三：画像説明（最悪なくてもよし）　四：スポットの名前またはタイトル
function spotitem(element,img_url,img_name,title_name,spot_id){
  element.insertAdjacentHTML('afterbegin', 
  `<div class="spot">
    <img src=../uploads/${img_url} alt=${img_name} id=${spot_id}>
    <h2>${title_name}</h2>
  </div>`);
}

//要素を入れて表示　第一：親要素　二：画像そのもの　三：画像説明（最悪なくてもよし）　四：スポットの名前またはタイトル
function diaryitem(element,img_url,img_name,title_name,spot_id){
  element.insertAdjacentHTML('afterbegin', 
  `<div class="diary">
    <img src=../uploads/${img_url} alt=${img_name} id=${spot_id}>
    <h2>${title_name}</h2>
  </div>`);
}

window.onload = function(){
document.querySelector('#section-item').addEventListener('click', (event) => {
  if(event.target.closest('.spot')){
    // console.log(event.target.closest('.spot'));
    const  parentitemid = event.target.closest('.spot').firstElementChild.getAttribute('id');
    console.log(parentitemid);
    window.location.href = `http://localhost/geocation/php/spot_detail.php?id=${parentitemid}`;
  }else{
    const  parentitemid = event.target.closest('.diary').firstElementChild.getAttribute('id');
    console.log(parentitemid);
      window.location.href = `http://localhost/geocation/php/diary_detail.php?id=${parentitemid}`;
  }
  
});

}
