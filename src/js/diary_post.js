document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    // デフォルトで★5つを選択状態にする
    stars.forEach(star => {
        if (star.getAttribute('data-value') <= 5) {
            star.classList.add('selected');
        }
    });

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = star.getAttribute('data-value');
            ratingInput.value = value; // 選択された評価を隠しフィールドにセットする

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