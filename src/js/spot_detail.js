// URLからspot_idを取得
const urlParams = new URLSearchParams(window.location.search);
const spot_id = urlParams.get('id');
console.log('受け取った値:', spot_id);

// DOM読み取り
const detail = document.querySelector('.detail');
const imgpass = document.querySelector('#imgpass');
const editDeleteButtons = document.querySelector('#edit-delete-buttons');

// PHPに送るデータをセット
const postdata = { imgid: spot_id ,branch: 1};
fetch('../spot_detailAPI.php', { // 第1引数に送り先
  method: 'POST', // メソッド指定
  headers: { 'Content-Type': 'application/json' }, // jsonを指定
  body: JSON.stringify(postdata)
})
  .then(response => response.json()) // 返ってきたレスポンスをjsonで受け取って次のthenへ渡す
  .then(res => {
    console.log(res);
    console.log(imgpass);
    console.log("USR_ID:" + res[0]['USER_ID']);
    imgpass.src = `./uploads/${res[0]['PHOTO']}`;
    // HTMLを生成
    detail.insertAdjacentHTML('afterbegin', 
    `<h2>${res[0]['SPOTNAME']}</h2>
    <p>満足度: <span>${res[0]['AVG_STERGOOD']}</span></p>  
    <p>登録者: <span>${res[0]['NAME']}</span></p>
    <p>費用: <span>${res[0]['COST']}</span></p>
    <p>住所: <span>${res[0]['ADDRESS']}</span></p>
    <div class="note">
      <p>備考欄</p>
      <p id="comment">${res[0]['REMARKS']}</p>
    </div>
    `);
  if (loggedInUserId === res[0]['NAME']) {
    editDeleteButtons.style.display = 'block';
}
})
  .catch(error => {
    console.log(error); // エラー表示
  });

window.onload = function() {
  document.querySelector('.diary-view-btn').addEventListener('click', (event) => {
    // 現在のスポットのIDを取得
    const spotId = new URLSearchParams(window.location.search).get('id');

    // PHPスクリプトを呼び出して日記のタイトルを取得
    fetch('../spot_detailAPI.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ imgid: spotId })
    })
    .then(response => response.json())
    .then(res => {
     // 現在のスポットのIDを取得
    const spotId = new URLSearchParams(window.location.search).get('id');
    console.log('送信するspot_id:', spotId);
      
      // ランキングページにリダイレクトし、タイトルをURLパラメータとして渡す
      window.location.href = `../ranking.php?spot_id=${encodeURIComponent(spotId)}&TYPE=2`;
    })
    .catch(error => {
      console.log(error); // エラー表示
    });
  });
  
  document.querySelector('.diary-post-btn').addEventListener('click', () => {
    const spotId = new URLSearchParams(window.location.search).get('id');
    if (spotId) {
      window.location.href = `../diary_post.php?spot_id=${spotId}`;
    } else {
      alert('スポットIDが見つかりません。');
    }
  });
}

// 編集ボタンをクリックしたときの処理
const editButton = document.getElementById('edit_button');
editButton.addEventListener('click', function(event) {
    event.preventDefault();
    
    // 現在のスポットのIDを取得
    const spotId = new URLSearchParams(window.location.search).get('id');

    // スポット編集ページにリダイレクト
    window.location.href = `../spot_edit.php?id=${spotId}`;
});


// 投稿を削除する
const deleteButton = document.querySelector('#remove_button');
deleteButton.addEventListener('click', function(event) {
    event.preventDefault();

    fetch('../delete_spot.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(postdata)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // 結果のメッセージを表示する
        window.location.href = './homepage.php'; // 削除が成功したらリダイレクトする
    })
    .catch(error => {
        console.error('エラー:', error);
        alert('投稿を削除する際にエラーが発生しました');
    });
});

function toggleProfileDropdown() {
  var dropdown = document.getElementById("profile-dropdown");
  dropdown.style.display = (dropdown.style.display === "none" || dropdown.style.display === "") ? "block" : "none";
}
document.addEventListener('click', function(event) {
    var dropdown = document.getElementById("profile-dropdown");
    var menu = document.querySelector('menu');
    if (!menu.contains(event.target) && event.target !== menu) {
        dropdown.style.display = 'none';
    }
});

let currentSlideIndex = {
  'spot-reco': 0,
  'spot-rank': 0,
  'diary-rank': 0
};

  function changeSlide(n, sectionId) {
    const slideContainer = document.getElementById(sectionId);
    const slides = slideContainer.children;
    const totalItems = slides.length;
    currentSlideIndex[sectionId] = (currentSlideIndex[sectionId] + n + totalItems) % totalItems;
    updateSlides(sectionId);
  }


function goToSlide(index, sectionId) {
  currentSlideIndex[sectionId] = index;
  updateSlides(sectionId);
}

function updateSlides(sectionId) {
  const slideContainer = document.getElementById(sectionId);
  const slides = slideContainer.children;
  const totalItems = slides.length;
  const dotsContainer = document.getElementById(`${sectionId}-dots`);
  for (let i = 0; i < totalItems; i++) {
    slides[i].style.transform = `translateX(-${currentSlideIndex[sectionId] * 100}%)`;
  }

}

document.addEventListener('DOMContentLoaded', () => {
  const sections = ['spot-reco', 'spot-rank', 'diary-rank'];
  sections.forEach(sectionId => {
    updateSlides(sectionId);
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.reco_slide img');
  const dots = document.querySelectorAll('.dot');

  slides.forEach((slide, index) => {
      if (index === 0) {
          slide.classList.add('active');
      }
  });

  
  dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
          currentSlide(index);
      });
  });
});


function updateSlide(index) {
  const slides = document.querySelectorAll('.reco_slide img');
  const dots = document.querySelectorAll('.dot');

  slides.forEach((slide, i) => {
      if (i === index) {
          slide.classList.add('active');
          dots[i].classList.add('active');
      } else {
          slide.classList.remove('active');
          dots[i].classList.remove('active');
      }
  });
}


function currentSlide(index) {
  updateSlide(index);
}