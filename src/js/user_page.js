document.addEventListener("DOMContentLoaded", function () {
  const user_posts = document.querySelector("#spot-posts");
  const user_diary = document.querySelector("#diary-posts");

  // まず、user_getin.phpを使ってユーザーIDを取得する
  fetch("../user_gatin.php", {
    method: "GET",
    headers: { "Content-Type": "application/json" },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error(data.error);
        return;
      }

      const user_id = data.user_id; // 取得したユーザーID
      const user_page = { user_id: user_id };
      console.log(user_page);

      // 次に、取得したユーザーIDを使ってuserAPI.phpを呼び出す
      return fetch("../userAPI.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(user_page),
      });
    })
    .then((response) => response.json())
    .then((res) => {
      console.log(res);
      if (res.error) {
        console.log(res.error);
        return;
      }
      res.DIARY.forEach((element) => {
        if (!element["PHOTO"]) {
          element["PHOTO"] = "../img/noimage.png";
        }
        diaryitem(
          user_diary,
          element["PHOTO"],
          element["TEXT"],
          element["TITLE"],
          element["DIARY_ID"]
        );
      });
      res.SPOT.forEach((element) => {
        if (!element["PHOTO"]) {
          element["PHOTO"] = "../img/noimage.png";
        }
        spotitem(
          user_posts,
          element["PHOTO"],
          element["REMAKES"],
          element["SPOTNAME"],
          element["SPOT_ID"]
        );
      });
    })
    .catch((error) => {
      console.log(error);
    });

  function diaryitem(element, img_url, img_name, title_name, diary_id) {
    element.insertAdjacentHTML(
      "afterbegin",
      `<div class="diary">
            <img src="./uploads/${img_url}" alt="${img_name}" id="${diary_id}">
            <h2>${title_name}</h2>
        </div>`
    );
  }

  function spotitem(element, img_url, img_name, title_name, spot_id) {
    element.insertAdjacentHTML(
      "afterbegin",
      `<div class="spot">
            <img src="./uploads/${img_url}" alt="${img_name}" id="${spot_id}">
            <h2>${title_name}</h2>
        </div>`
    );
  }

  document.querySelector("#section-item").addEventListener("click", (event) => {
    if (event.target.closest(".diary")) {
      const parentitemid = event.target
        .closest(".diary")
        .firstElementChild.getAttribute("id");
      window.location.href = `../diary_detail.php?id=${parentitemid}`;
    }
    if (event.target.closest(".spot")) {
      const parentitemid = event.target
        .closest(".spot")
        .firstElementChild.getAttribute("id");
      window.location.href = `../spot_detail.php?id=${parentitemid}`;
    }
  });
});
function toggleProfileDropdown() {
  var dropdown = document.getElementById("profile-dropdown");
  dropdown.style.display =
    dropdown.style.display === "none" || dropdown.style.display === ""
      ? "block"
      : "none";
}
document.addEventListener("click", function (event) {
  var dropdown = document.getElementById("profile-dropdown");
  var menu = document.querySelector("menu");
  if (!menu.contains(event.target) && event.target !== menu) {
    dropdown.style.display = "none";
  }
});

let currentSlideIndex = {
  "spot-reco": 0,
  "spot-rank": 0,
  "diary-rank": 0,
};

function changeSlide(n, sectionId) {
  const slideContainer = document.getElementById(sectionId);
  const slides = slideContainer.children;
  const totalItems = slides.length;
  currentSlideIndex[sectionId] =
    (currentSlideIndex[sectionId] + n + totalItems) % totalItems;
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
    slides[i].style.transform = `translateX(-${
      currentSlideIndex[sectionId] * 100
    }%)`;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const sections = ["spot-reco", "spot-rank", "diary-rank"];
  sections.forEach((sectionId) => {
    updateSlides(sectionId);
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const slides = document.querySelectorAll(".reco_slide img");
  const dots = document.querySelectorAll(".dot");

  slides.forEach((slide, index) => {
    if (index === 0) {
      slide.classList.add("active");
    }
  });

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      currentSlide(index);
    });
  });
});

function updateSlide(index) {
  const slides = document.querySelectorAll(".reco_slide img");
  const dots = document.querySelectorAll(".dot");

  slides.forEach((slide, i) => {
    if (i === index) {
      slide.classList.add("active");
      dots[i].classList.add("active");
    } else {
      slide.classList.remove("active");
      dots[i].classList.remove("active");
    }
  });
}

function currentSlide(index) {
  updateSlide(index);
}
