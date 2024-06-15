//DOM読み取り
const results = document.querySelector('#results');
const searchboxvalue = document.querySelector('#searchbox');
const radioboxitem = document.querySelector('#radioboxitem');
const listitem = document.querySelector('#listitem');
var baranch_num = 0
setradiobotton_update();

//スポットランキング時処理
document.querySelector('#spot-ranking').addEventListener('click', () => {
  removeresultitems();
  removeserchbox_items();
  removeradiobotton();
  setradiobotton_order();
  baranch_num = 1;
  setitem(1,"",2);
});

//日記ランキング時処理
document.querySelector('#diary-ranking').addEventListener('click', () => {
  removeresultitems();
  removeserchbox_items();
  removeradiobotton();
  setradiobotton_order();
  baranch_num = 2;
  createresult_items(2,"",2);
});

//抽象化クリエイト
function createresult_items(branch,sort_text,radiovalue){
  setitem(branch,sort_text,radiovalue);
}
//検索項目時処理
document.querySelector('#search-button').addEventListener('click', () => {
  removeresultitems();
  removeserchbox_items();
  removeradiobotton();
  serchbox_items();
  setradiobotton_update();
});

//ラジオボタン削除
function removeradiobotton(){
  while(radioboxitem.firstChild){
    radioboxitem.removeChild(radioboxitem.firstChild);
  }
}

//検索ボックス表示
function serchbox_items(){
  listitem.insertAdjacentHTML('afterbegin', 
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
    </div>`);
}

//検索ボックス削除
function removeserchbox_items(){
  const ranking_select = document.querySelector('#ranking-select');
  if(ranking_select){
  ranking_select.remove();
  }
}

//昇順降順ラジオボタン表示
function setradiobotton_order(){
  radioboxitem.insertAdjacentHTML('afterbegin', 
    `<input type="radio" name="radiobotton" value="1">昇順
    <input type="radio" name="radiobotton" value="2">降順`);
}

//更新日時変更ラジオボタン表示
function setradiobotton_update(){
  radioboxitem.insertAdjacentHTML('afterbegin', 
    `<input type="radio" name="radiobotton" value="3">新しい順
    <input type="radio" name="radiobotton" value="4">古い順`);
}


//ラジオボタン読み取り
function checkradiobotton(){
  if(document.radiobottonitem.radiobotton.value){
    return document.radiobottonitem.radiobotton.value;
  }
}

//結果表示を消す
function removeresultitems(){
  //既にある要素を削除
  while(results.firstChild){
    results.removeChild(results.firstChild);
  }
}
//APIにデータを送って表示
//以下引数(第一：日記スポットかあるいはどっちも、第二：検索文字、第三：ラジオボタンの入力（value))
function setitem(resbranch,sort_text,radiovalue){
  const branchdata = {branch:resbranch,sort_text:sort_text,radiovalue:radiovalue}
  console.log(branchdata);
  fetch('http://localhost/geocation/php/API.php', { // 第1引数に送り先
    method: 'POST', // メソッド指定
    headers: { 'Content-Type': 'application/json' }, // jsonを指定
    body:JSON.stringify(branchdata)
  })
    .then(response => response.json()) // 返ってきたレスポンスをjsonで受 け取って次のthenへ渡す
    .then(res => {
      console.log(res)
      if(resbranch == 0 || resbranch == 1){
        res[0]["SPOT"].forEach(element => {
          resultitem(results,element['PHOTO'],element['SPOTNAME'],element['SPOTNAME'],element["SPOT_ID"],element["REMARKS"])
        });
      }
      if(resbranch == 0 || resbranch == 2){
        res[0]["DIARY"].forEach(element => {
          if(!element['PHOTO']){
            element['PHOTO'] = '../img/noimage.png'
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
//ソート結果を表示
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


//検索機能
listitem.addEventListener('click', (event) => {
  if(event.target.closest('#searchicon')){
    removeresultitems();
    //ラジオボタン、入力、プルダウンの読み取り
    const radiovalue = checkradiobotton();
    const searchboxvalue = document.querySelector('#searchbox').value;
    const pulldownvalue = document.querySelector('#selectsort').value;
    if(!searchboxvalue == ""){
      setitem(pulldownvalue,searchboxvalue,radiovalue);
    }else{
      //要素削除
      removeresultitems();
    }
  }
  //ラジオボタンを変更したとき
  if(event.target.closest('[type="radio"]')){
    const radiovalue = checkradiobotton();
    if(radiovalue == 1 || radiovalue == 2 ){
      removeresultitems();
      setitem(baranch_num,"",radiovalue);
    }
  }
});


