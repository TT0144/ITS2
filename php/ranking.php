<?php 

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../css/ranking.css">
</head>
<body>
  <!--//ヘッダー部分-->
  <header>
    <div class="header-left">
        <h1><img src="../image/logo.png" alt="Geocation ロゴ"></h1>
        <nav>
            <ul>
                <li class="menu-item"><a href="./diary.html">日記投稿</a></li>
                <li class="menu-item"><a href="./spot.html">スポット投稿</a></li>
                <li class="menu-item"><a href="./ranking.html">ランキング・検索</a></li>
            </ul>
        </nav>
    </div>
    <div class="header-right">
        <a href="../login.html" class="login-button">新規登録・ログイン</a>
    </div>
</header>

  <main>
    <div id="split">
      <div id="split-child">
        <div id="button-group">
          <button class="buttonsec">スポットランキング</button>
          <button class="buttonsec">日記ランキング</button>
          <button class="buttonsec">検索</button>
        </div>
      </div>
      <div id="listitem">
        <div id="ranking-select">
          <select name="sort" id="selectsort">
            <option value="0">すべて</option>
            <option value="1">スポット</option>
            <option value="2">日記</option>
          </select>
          <input type="text" value="aa" id="serchbox"></input>
          <div id="serchicon">
            <img src="../image/虫眼鏡の無料アイコン8.png" alt="虫眼鏡アイコン" width="20" height="21">
          </div>
        </div>
        <div class="resultitem">
          <div class="itemimg">
            <img src="spot1.jpg" alt="Spot 1">
          </div>
          <div class="item-explan">
            <div class="list-title">タイトル</div>
            <div>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</div>
          </div>
        </div>
        <div class="resultitem">
          <div class="itemimg">
            <img src="spot1.jpg" alt="Spot 1">
          </div>
          <div class="item-explan">
            <div class="list-title">タイトル</div>
            <div>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</div>
          </div>
        </div>
        <div class="resultitem">
          <div class="itemimg">
            <img src="spot1.jpg" alt="Spot 1">
          </div>
          <div class="item-explan">
            <div class="list-title">タイトル</div>
            <div>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</div>
          </div>
        </div>
        <div class="resultitem">
          <div class="itemimg">
            <img src="spot1.jpg" alt="Spot 1">
          </div>
          <div class="item-explan">
            <div class="list-title">タイトル</div>
            <div>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</div>
          </div>
        </div>
        <div class="resultitem">
          <div class="itemimg">
            <img src="spot1.jpg" alt="Spot 1">
          </div>
          <div class="item-explan">
            <div class="list-title">タイトル</div>
            <div>テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト</div>
          </div>
        </div>

      </div>
    </div>
  </main>
</body>
</html>