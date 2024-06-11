document.addEventListener('DOMContentLoaded', function() {
  const stars = document.querySelectorAll('.star');

// デフォルトで★5つを選択状態にする
stars.forEach(star => {
    if (star.getAttribute('data-value') <= 5) {
        star.classList.add('selected');
    }
});

stars.forEach(star => {
    star.addEventListener('click', () => {
        const value = star.getAttribute('data-value');
        stars.forEach(s => {
            if (s.getAttribute('data-value') <= value) {
                s.classList.add('selected');
            } else {
                s.classList.remove('selected');
            }
        });
    });
});

    // 写真のプレビュー表示の設定
    const photoInput = document.getElementById('photo');
    const photoLabel = document.getElementById('photo-label');
    const photoPreview = document.getElementById('photo-preview');

    photoInput.addEventListener('change', () => {
        const file = photoInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '100%';
                img.style.borderRadius = '5px';
                photoPreview.innerHTML = '';
                photoPreview.appendChild(img);
                photoLabel.style.display = 'none'; // 写真が表示されたらラベルを非表示
                photoPreview.style.border = 'none'; // 写真が表示されたら枠を非表示
                photoPreview.style.backgroundColor = 'transparent'; // 背景も非表示
            };
            reader.readAsDataURL(file);
        }
    });
});

// 遷移しながら値を渡す
document.querySelector('#addcomp').addEventListener('click', () => {
  const imgid = '../img/facebook.png'
  window.location.href = `http://localhost/geocation/html/diary_detail.html?value=${imgid}`;
});

//詳細画面からのリダイレクト拒否
