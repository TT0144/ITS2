document.addEventListener('DOMContentLoaded', () => {
    const iconInput = document.getElementById('icon');
    const photoLabel = document.getElementById('photo-label');
    const photoPreview = document.getElementById('photo-preview');

    if (iconInput) {
        iconInput.addEventListener('change', () => {
            const file = iconInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '100%';
                    img.style.objectFit = 'contain'; // 画像を親要素に収める
                    img.style.height = '100%'; // 高さを枠に合わせる
                    img.style.width = 'auto'; // 横幅を自動調整
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
    } else {
        console.error('Icon input element not found');
    }
});
