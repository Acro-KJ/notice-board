<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
    <link rel="stylesheet" type="text/css" href="mission5.css">
</head>
<body>


    <!--mission5-1-->

    <?php
    //"Notice"の非表示
        error_reporting(E_ALL & ~E_NOTICE);
    //データベースの事前準備    
	    // DB接続設定
        $dsn = 'mysql:dbname=********;host=********';
        $user = '*********';
        $password = '*************';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //データベース内にテーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS trialboard"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "password TEXT"
        .");";
        $stmt = $pdo->query($sql);

        /*
        //データベースのテーブル一覧を表示
        $sql ='SHOW TABLES';
        $result = $pdo -> query($sql);
        foreach ($result as $row){
            echo $row[0];
            echo '<br>';
        }
        echo "<hr>";

        //作成したテーブルの構成詳細を確認
        $sql = "SHOW CREATE TABLE trialboard";
        $result = $pdo -> query($sql);
        foreach($result as $row){
            echo $row[0];
        }
        echo "<hr>";
        */
    
    //変数の定義
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        
        $d_password=$_POST["dpassword"];
        $e_password=$_POST["epassword"];
        
        $d_number=$_POST["dnumber"];
        $e_number=$_POST["enumber"];
        $E_name=$_POST["ename"];
        $E_comment=$_POST["ecomment"];

        //編集フォームに入力する名前・コメントの呼び出し
        if(strlen($e_number)>0){
            $id = $e_number; ///変更したい投稿番号

            $sql ='SELECT * FROM trialboard WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('id',$id,PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //echo "編集フォームに入力するもの　".$row['id'].',';
                $e_name = $row['name'];
                $e_comment = $row['comment'];
                //echo $e_name."　";
                //echo $e_comment."<br>";
            }
        }
    
    //パスワードを見つける準備
        //入力したデータレコードを抽出し、表示
        if(strlen($d_number)>0){
            $id = $d_number; ///データを抽出したいID
        }elseif(strlen($e_number)>0){
            $id = $e_number; ///データを抽出したいID
        }
        
        $sql ='SELECT * FROM trialboard WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('id',$id,PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの添え字([]内)は、作成したカラムの名称に合わせる必要あり
            //echo "正しいパスワード　".$row['id'].',';
            $checkcord = $row['password'];
            //echo $checkcord;
        }
        //echo "<hr>";  

    ?>

    <form action="" method="post">
        <input type="text" name="name" placeholder="名前">
        <input type="text" name="comment" placeholder="コメント">
        <input type="submit" name="submit">
        <br>
    </form>
    <form action="" method="post">
        <input type="text" name="dnumber" placeholder="削除対象番号">
        <input type="text" name="dpassword" placeholder="パスワード">
        <button type="submit" name="submit">削除</button>
        <br>
    </form>
    <form action="" method="post">
        <input type="text" name="enumber" placeholder="編集対象番号" value=<?php if(strlen($e_number)>0 && strlen($E_name)==0){echo $e_number;}?>>
        <input type="text" name="epassword" placeholder="パスワード" value =<?php if(strlen($e_number)>0 && $e_password==$checkcord && strlen($E_name)==0){echo $e_password;}?>>
        <input type="text" name="ename" placeholder="未入力でOK" value =<?php if(strlen($e_number)>0 && $e_password==$checkcord && strlen($E_name)==0){echo $e_name;}?>>
        <input type="text" name="ecomment" placeholder="未入力でOK" value =<?php if(strlen($e_number)>0 && $e_password==$checkcord && strlen($E_name)==0){echo $e_comment;}?>>
        <button type="submit" name="submit">編集</button>
    </form>



    
    <?php    
    //投稿機能
        if(strlen($comment)>0 && strlen($name)>0){
            //必要なデータ生成
            $date = date("Y-m-d h:i:s");
            $password = chr(mt_rand(97, 122)).chr(mt_rand(97, 122)).chr(mt_rand(97, 122)).chr(mt_rand(97, 122));
            
            //データをデータベースへ送信
            $sql = $pdo -> prepare("INSERT INTO trialboard (name,comment,date,password) VALUES (:name, :comment, :date, :password)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql -> execute();

    //削除機能()
        }elseif(strlen($d_number)>0 && $d_password==$checkcord){
            $id = $d_number;

            $sql = 'delete from trialboard where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt ->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt ->execute();    
        
    //編集機能
        }elseif(strlen($e_number)>0 && $e_password==$checkcord && strlen($E_name)>0){
            $id = $e_number; ///変更したい投稿番号

           
            //$e_name = "名無し";
            //$e_comment ="ハロー！";
            $sql ='UPDATE trialboard SET name=:name,comment=:comment WHERE id=:id';
            
            $stmt = $pdo->prepare($sql);
            $stmt ->bindParam(':name',$E_name,PDO::PARAM_STR);
            $stmt ->bindParam(':comment',$E_comment,PDO::PARAM_STR);
            $stmt ->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt ->execute();
    
        }

    //投稿・削除・編集の説明とFB
        echo "<h3>使用方法</h3>";
        echo "新規投稿時：　「名前」と「コメント」を入力<br>";
        echo "削除・編集時：　「削除・編集対象番号」と「パスワード」を入力<br><br>";

        if(strlen($comment)>0 && strlen($name)>0){
            echo "<strong>最新の投稿<br><br>投稿者：".$name."　コメント：".$comment."<br>パスワード：".$password."</strong>";
        }elseif(strlen($d_number)>0){
            if($d_password==$checkcord){
                echo "<strong>投稿番号 ".$d_number." を削除しました</strong>";
            }elseif(strlen($d_password)>0 && $d_password!==$checkcord){
                echo "<strong>パスワードが違います</strong>";
            }else{
                echo "<strong>パスワードが入力されていません</strong>";
            }
        }elseif(strlen($e_number)>0){
            if($e_password==$checkcord && strlen($E_name)==0){
                echo "<strong>「名前」、「コメント」を編集してください</strong>";
            }elseif($e_password==$checkcord && strlen($E_name)>0){
                echo "投稿番号 ".$e_number." を編集しました</strong>";
            }elseif(strlen($e_password)>0 && $e_password!==$checkcord){
                echo "<strong>パスワードが違います</strong>";
            }else{
                echo "<strong>パスワードが入力されていません</strong>";
            }
        }


    //履歴の表示	
        $sql = 'SELECT * FROM trialboard';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        echo "<br><br><h3>掲示板</h3>";
    ?>

    <div class="box7">
    <p>
    <?php
        foreach($results as $row){
            //$rowrowの中にはテーブルのカラム名が入る
            echo $row['id'].'　';
            echo $row['name'].'　';
            echo $row['date'].'<br>';
            echo $row['comment'].'<br>';
            echo $row['password'].'<br><br>';
        }
        echo "<hr>";
    
	
    /*
    //投稿内全削除
        $sql ='DROP TABLE trialboard';
        $stmt = $pdo->query($sql);
        
    */
	
    ?>
    </p>
    </div>
    


</body>
</html>