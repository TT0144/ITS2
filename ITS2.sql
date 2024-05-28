--ここはrootでやること
----DBの作成
 CREATE DATABASE IF NOT EXISTS ITS2;
-- testの中のITS1領域に権限付与
GRANT ALL ON ITS2.* TO dbuser;
-- -- 使うDBの選択
USE ITS2;
