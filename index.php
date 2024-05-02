<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>X</title>
<link rel="stylesheet" type="text/css" href="./CSS/style.css">
</head>
<script src=./Javascript/1st_script.js></script>
<body>

    <section class="left">
        <a href="index.php"><img src="./images/logo.gif" style="width: 70px;"></a>
        <br />

        <div class="home">
            <a href="index.php"><img src="./images/home.png" style="width: 50px;"></a>
            <a href="index.php" style="text-decoration: none;">HOME</a>
        </div>

            <?php
            // leftから選択された名前を取得
            $name = '';
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
                $name = $_POST['name'];
                // 名前がnullの場合、"GUEST"を設定
                if (empty($name)) {
                    $name = "GUEST";
                }
            }
            ?>

        <div class="profile">
            <!-- おかえりなさいメッセージ -->
            <div class="welcome">
                <?php
                if (!empty($name)) {
                    echo "おかえりなさい " . htmlspecialchars($name, ENT_QUOTES) . " さん。";
                } else {
                    echo "ユーザー名が設定されていません。";
                }
                ?>
            </div><br />

            <!-- 画像表示 -->
            <?php
            $imagePath = 'images/' . $name . '.gif';
            if (file_exists($imagePath)) {
                echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($name, ENT_QUOTES) . '" style="max-width: 100px; max-height: 100px;">';
            }
            ?>
            <br /><br />
            <P>ユーザー名設定</P>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <select name="name">
                    <option value="GUEST" <?php if ($name == 'GUEST') echo 'selected'; ?>>GUEST</option>
                    <option value="USER1" <?php if ($name == 'USER1') echo 'selected'; ?>>USER1</option>
                    <option value="USER2" <?php if ($name == 'USER2') echo 'selected'; ?>>USER2</option>
                    <option value="USER3" <?php if ($name == 'USER3') echo 'selected'; ?>>USER3</option>
                </select><br /><br />
                <!-- 設定ボタン -->
                <button type="submit" name="set_name">設定</button>
            </form>
        </div>

        <div class="ranking">
                <h3>総いいね数ランキング</h3>
            <ul style="list-style-type: none;">
                <?php
                    try {
                        $pdo = new PDO('mysql:host=127.0.0.1;dbname=phpdb;charset=utf8', 'root', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stmt = $pdo->query('SELECT name, SUM(`like`) AS total_likes FROM messages GROUP BY name ORDER BY total_likes DESC');
                        $rank = 1;
                        $prev_likes = null; // 直前のユーザーの総いいね数を保存
                        $skipped_ranks = 0; // スキップされたランクの数
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // 同率の場合の処理
                            if ($prev_likes !== null && $row['total_likes'] == $prev_likes) {
                                // 直前のユーザーと同じ総いいね数ならば同じ順位とする
                                $rank--;
                                $skipped_ranks++; // スキップされたランクをカウント
                            } else {
                                // スキップされたランクがある場合、それを適用する
                                $rank += $skipped_ranks;
                                $skipped_ranks = 0; // スキップされたランクの数をリセット
                            }

                            // ランクに応じた画像を表示
                            if ($rank == 1) {
                                echo '<li><img src="./images/ranking/1.png" alt="First Rank Image" style="max-width: 25px;"> ' . $row['name'] . '<br />（' . $row['total_likes'] . 'いいね）</li>';
                            } elseif ($rank == 2) {
                                echo '<li><img src="./images/ranking/2.png" alt="Second Rank Image" style="max-width: 25px;"> ' . $row['name'] . '<br />（' . $row['total_likes'] . 'いいね）</li>';
                            } elseif ($rank == 3) {
                                echo '<li><img src="./images/ranking/3.png" alt="Third Rank Image" style="max-width: 25px;"> ' . $row['name'] . '<br />（' . $row['total_likes'] . 'いいね）</li>';
                            } else {
                                echo '<li><img src="./images/ranking/4.png" alt="Rank Image" style="max-width: 25px;"> ' . $row['name'] . '<br />（' . $row['total_likes'] . 'いいね）</li>';
                            }

                            // 直前のユーザーの総いいね数を更新
                            $prev_likes = $row['total_likes'];
                            $rank++;
                        }
                    } catch (PDOException $e) {
                        exit('データを取得できませんでした。');
                    }
                ?>
            </ul>
        </div>

    </section>

    <section class="center">
        <?php
        // メッセージ削除の処理
        if (isset($_POST['delete_submit'])) {
            try {
                $pdo = new PDO('mysql:host=127.0.0.1;dbname=phpdb;charset=utf8', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                exit('データベースに接続できませんでした。');
            }

            $delete_id = $_POST['delete'];
            try {
                $stmt = $pdo->prepare("DELETE FROM messages WHERE no = :id");
                $stmt->bindParam(':id', $delete_id);
                $stmt->execute();
                echo "<script>showMessageDelete();</script>";
            } catch (PDOException $e) {
                exit('データを削除できませんでした。');
            }

            $pdo = null;
        }

        // いいねボタンがクリックされたときの処理
        if (isset($_POST['like_submit'])) {
            try {
                $pdo = new PDO('mysql:host=127.0.0.1;dbname=phpdb;charset=utf8', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                exit('データベースに接続できませんでした。');
            }

            $like_id = $_POST['like_id'];
            try {
                $stmt = $pdo->prepare("UPDATE messages SET `like` = `like` + 1 WHERE no = :id");
                $stmt->bindParam(':id', $like_id);
                $stmt->execute();
                echo "<script>showMessageLike();</script>";
            } catch (PDOException $e) {
                exit('いいねを追加できませんでした。');
            }

            $pdo = null;
        }

        // フォームが送信されたかどうかを確認
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // メッセージが空でないことを確認
            if (!empty($_POST['message'])) {
                try {
                    $pdo = new PDO('mysql:host=127.0.0.1;dbname=phpdb;charset=utf8', 'root', '');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    exit('データベースに接続できませんでした。');
                }

                $message = $_POST['message'];
                $created = date('Y-m-d');

                try {
                    $stmt = $pdo->prepare("INSERT INTO messages (name, message, created, `like`) VALUES (:name, :message, :created, :like)");
                    $stmt->bindParam(':name', $name); // leftから取得した名前を使用
                    $stmt->bindParam(':message', $message);
                    $stmt->bindParam(':created', $created);
                    $like = 0; // ここでlikeカラムに0を設定
                    $stmt->bindParam(':like', $like);
                    $stmt->execute();
                    // JavaScriptの関数を呼び出してアラートを表示
                    echo "<script>showMessageSuccese();</script>";
                } catch (PDOException $e) {
                    exit('データを登録できませんでした。');
                }

                $pdo = null;
            } else {
                // メッセージが空の場合はエラーを表示
                echo "<p>メッセージは必須です。</p>";
            }
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <textarea name="message" cols="10" rows="5" placeholder="いまなにしてる？"></textarea><br />
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>"> <!-- ここでnameをhiddenで送信 -->
            <br />
            <div class="post">
                <button type="submit" name="post_submit">ポストする</button>
            </div>
        </form>

        <?php
        // メッセージ一覧を表示
        try {
            $pdo = new PDO('mysql:host=127.0.0.1;dbname=phpdb;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit('データベースに接続できませんでした。');
        }

        try {
            $stmt = $pdo->query('SELECT * FROM messages ORDER BY no DESC LIMIT 15');
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div style='display:flex; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 10px; margin-top:5%;'>\n";
                echo "<div style='flex:1;'>\n";
                // 名前に対応する画像を表示
                $imageName = 'images/' . $data['name'] . '.gif';
                if (file_exists($imageName)) {
                    echo '<img src="' . $imageName . '" style="max-width:100px; max-height:100px;">';
                } else {
                    echo 'ユーザー名が設定されていません';
                }
                echo "</div>\n";
                echo "<div style='flex:8; margin-left: 5%;'>\n";
                echo '<strong style="color: black;">' . htmlspecialchars($data['name'], ENT_QUOTES) . ' <span style="color: grey; font-size:12px">' . $data['created'] . "</span></strong><br />\n";
                echo '<div style="display:flex; align-items:center;">';
                echo '<div style="padding-left: 10px;">' . nl2br(htmlspecialchars($data['message'], ENT_QUOTES)) . '</div>';
                echo '<div style="margin-left: auto;">';
                echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
                echo '<input type="hidden" name="like_id" value="' . $data['no'] . '">';
                echo '<button type="submit" name="like_submit">いいね (' . $data['like'] . ')</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
                echo '<input type="hidden" name="delete" value="' . $data['no'] . '">';
                echo '<br />';
                echo '<div style="text-align: right;">';
                echo '<button type="submit" name="delete_submit">削除</button>';
                echo '</div>';
                echo '</form>';
                echo "</div>\n";
                echo "</div>\n";
            }
        } catch (PDOException $e) {
            exit('データを取得できませんでした。');
        }

        $pdo = null;
        ?>
    </section>

    <section class="right">
        <div class="right_top">
            <h3>現在のトレンド画像</h3>
            <p id="selectedKeywordDisplay"></p>
            <button id="toggleGalleryButton">非表示にする</button>
        </div>
        <div class="gallery_frame">
            <div id="gallery"></div>
        </div>
    </section>
</body>
<script src=./Javascript/2nd_script.js></script>
</html>