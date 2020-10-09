<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>forum.php</title>
</head>
<body>
    
<?php
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
    // DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	if ($pdo) {
            echo "DB接続OK<br>";
        } else {
            echo "DB接続NG<br>";
        }
	$sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "time TEXT,"
    . "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
    if(!empty($_POST["name"])){
        //名前
        $name = $_POST["name"];
    }
    
    if(!empty($_POST["comment"])){
        //コメント
        $comment = $_POST["comment"];
    }
    
    if(!empty($_POST["editnum"])){
        //編集番号
        $editnum = $_POST["editnum"];
    }
    
    if(!empty($_POST["delete"])){
        //削除番号
        $delete = $_POST["delete"];
    }
    
    if(!empty($_POST["edit"])){
        //編集番号
        $edit = $_POST["edit"];
    }
    
    if(!empty($_POST["editnum"])){
        //編集番号（フォーム）
        $editnum = $_POST["editnum"];
    }
    
    if(!empty($_POST["pass"])){
        //投稿パスワード
        $pass = $_POST["pass"];
    }
    
    if(!empty($_POST["pass_delete"])){
        //削除パスワード
        $pass_delete = $_POST["pass_delete"];
    }
    
    if(!empty($_POST["pass_edit"])){
        //編集パスワード
        $pass_edit = $_POST["pass_edit"];
    }
     
    $time = date("Y/m/d H:i:s");
       
    //投稿フォームの条件分岐（名前とコメントとパスワードが空でない場合 + 編集番号が空である場合）
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["editnum"]) && !empty($_POST["pass"])){
        
    	//5.データを入力（データレコードの挿入）
        //prepare関数：query関数と似ている。ユーザからの入力をSQL文に含めることができる
        //INSERT テーブル名 (カラム名) VALUES(ユーザからの入力情報)
        $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
        //bindParam：execute()された時に変数を評価する。値の参照を受け取る。	
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        //bindParam：prepare関数の時に使うSQL文の中で値を繋げるための関数。
        //PDO::PARAM_STR：指定された変数名にパラメータをバインドする
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':time', $time, PDO::PARAM_STR);
        $sql-> bindParam(':pass', $pass, PDO::PARAM_STR);
        //execute関数：プリペアドステートメントを実行する際に使う。
        //プリペアドステートメント：SQL文で値が変わる可能性がある箇所に対し
        // 変数のように別の文字列を入れておきあとで置き換える仕組み
        $sql -> execute();
        echo"投稿に成功しました。<br>";
    }else{
        echo"投稿してください。<br>";
    }
        //削除処理
    if(!empty($_POST["delete"])){
        $sql = 'SELECT * FROM mission5';
        $stmt = $pdo -> query($sql);
        $results = $stmt -> fetchAll();
        foreach($results as $row){
            if($row['pass']  == $pass_delete){
                $id = $_POST["delete"];
                $sql = 'delete from mission5 where id=:id';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                $stmt -> execute();
            
            }elseif($row["pass"]  != $row["pass_delete"]){
                echo"パスワードが違います。";
            }
       }
    }
    //フォームへの編集投稿,番号の表示 
    if(!empty($_POST["edit"])){
        $spl = 'SELECT * FROM mission5';
        $stmt = $pdo -> query($spl);
        $results = $stmt -> fetchAll();
        foreach($results as $row){
            if($row['id'] == $edit && $row['pass'] == $pass_edit){
                $edit_num = $row['id'];
                $edit_name = $row['name'];
                $edit_comment = $row['comment'];
            }elseif($_POST["edit"] == $row['id'] && $_POST["pass_edit"] != $row['pass']){
                echo"パスワードが違います";
            }
        }
    }elseif(!empty($_POST["edit"]) && empty($_POST("pass_edit"))){
        echo"パスワードを入力してください";
        //名前、コメント、編集番号が空でなければ
    }elseif(!empty($_POST["name"]) && !empty($_POST["name"]) && !empty($_POST['editnum'])){
        $id = $_POST["editnum"];
        //UPDATE テーブル名 SET カラム名１＝：値１,カラム名２＝：値２ WHERE 条件式
        //テーブルに格納されているデータを新しい値に更新するための文法
        $sql = 'UPDATE mission5 SET name=:name,comment=:comment WHERE id=:id';
        //prepare関数：query関数と似ている。ユーザからの入力をSQL文に含める
        $stmt5 = $pdo->prepare($sql);
        //bindParam：execute()された時に変数を評価する。値の参照を受け取る。
        $stmt5 -> bindParam(':name', $name, PDO::PARAM_STR);
        //PDO::PARAM_STR：指定された変数名にパラメータをバインドする
        $stmt5 -> bindParam(':comment', $comment, PDO::PARAM_STR);
        //PDO::PARAM_INT：boolをintに変換する
        $stmt5 -> bindParam(':id', $id, PDO::PARAM_INT);
        //execute関数：プリペアドステートメントを実行する際に使う。
        $stmt5 -> execute();
    }
?>
    【入力フォーム】<br>
    <form action="" method="post">
    <input type="text" name="name" placeholder = "名前" value = "<?php if(!empty($edit)){echo $edit_name;} ?>"><br>
    <input type="text" name="comment" placeholder = "コメント" value= "<?php if(!empty($edit)){echo $edit_comment;} ?>"><br>
    <input type="hidden" name="editnum" value= "<?php if(!empty($edit)){echo $edit_num;} ?>">
    <input type="password" name="pass" placeholder = "パスワード"><br>
    <input type="submit" value="送信"><br>
    【削除フォーム】<br>
    <form action="" method="post">
    <input type="text" name="delete" placeholder="削除対象番号"><br>
    <input type="password" name="pass_delete" placeholder = "パスワード"><br>
    <input type="submit" value="削除"><br>
    【編集フォーム】<br>
    <form action="" method="post">
    <input type="text" name="edit" placeholder="編集対象番号"><br>
    <input type="password" name="pass_edit" placeholder = "パスワード">
    <input type="submit" value="編集"><br>
    </form>
<?php
	//SELECT * FROM テーブル名:
    //*によってテーブルに登録された全てのレコードを選択
    //FROM テーブル名で表示するテーブル名を指定
    $sql = 'SELECT * FROM mission5';
    //query関数：指定したSQL文をデータベースに発行。
    //->：アロー演算子。左辺から右辺を取り出す。
    //クラスのメソッドやプロパティにアクセスする時に使う
    $stmt = $pdo -> query($sql);
    //・fetchAll：配列を作成
    $results = $stmt -> fetchAll();
    //ループ
    foreach ($results as $row){
	    //$rowの中にはテーブルのカラム名が入る
	    echo $row['id'].',';
	    echo $row['name'].',';
	    echo $row['comment'].',';
	    echo $row['time'].'<br>';
    }
?>
</body>
</html>