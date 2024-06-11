// 値を受け取る
const urlParams = new URLSearchParams(window.location.search);
const backurl = urlParams.get('value');
console.log('受け取った値:', backurl);


//戻るボタンを押して前画面に遷移
document.querySelector('.BackButton').addEventListener('click', () => {
  history.back();
  // window.location.href = 'http://localhost/geocation/html/diary_detail.html';
});
