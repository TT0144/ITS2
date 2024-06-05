//スポットアイテムを押した場合
document.querySelector('.spot').addEventListener('click', () => {
  const backurl = 'http://localhost/geocation/html/homepage.html';
  window.location.href = `http://localhost/geocation/html/spot_detail.html?value=${backurl}`;
});

//日記アイテムを押した場合
document.querySelector('.diary').addEventListener('click', () => {
  const backurl = 'http://localhost/geocation/html/homepage.html';
  window.location.href = `http://localhost/geocation/html/diary_detail.html?value=${backurl}`;
});

//使い方
const spot_rank = document.querySelector('#spot-rank');
homeitem(spot_rank);

//要素を入れて表示　第一：親要素　二：画像そのもの　三：画像説明（最悪なくてもよし）　四：スポットの名前またはタイトル
function homeitem(element,img_url,img_name,title_name){
  element.insertAdjacentHTML('afterbegin', 
  `<div class="spot">
    <img src=${img_url} alt=${img_name}>
    <h2>${title_name}</h2>
  </div>`);
}
