<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>m5-01.php</title>
    
  </head>
  <body>

  <?php
     //サーバーに接続
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $pass = 'パスワード';
    $pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //テーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS tbmm5_1"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
  . "comment TEXT,"
  . "nichiji TIMESTAMP,"
  . "password char(10)"
	.");";
  $stmt = $pdo->query($sql);

//フォームが空でなかったら
  if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['pass'])) {
//変数を定義
   $name = $_POST['name'];
   $comment = $_POST['comment'];
   $password = $_POST['pass'];
  //$nichiji = date("Y年m月d日 H:i:s");

  // editNoがないときは新規投稿、ある場合は編集 ***ここで判断
    if (empty($_POST['editNO'])) {

      $sql = $pdo -> prepare("INSERT INTO tbmm5_1 (name, comment, nichiji, password) VALUES (:name, :comment, :nichiji, :password)");
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
      $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
      $sql -> bindValue(':nichiji', date('Y-m-d H:i:s'), PDO::PARAM_STR);
      $sql -> bindParam(':password', $password, PDO::PARAM_STR);
    $sql -> execute();
    echo "投稿しました。";
    
    } else {
    // 以下編集機能
    $editNO = $_POST['editNO'];
    $id = $editNO; 
    $sql = 'UPDATE tbmm5_1 SET name=:name,comment=:comment,nichiji=:nichiji,password=:password WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
    $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt -> bindValue(':nichiji', date('Y-m-d H:i:s'), PDO::PARAM_STR);
    $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
	  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    echo $id. "を編集しました。<br>";
    }
  }



     //編集フォームの送信の有無で処理を分岐
  if (!empty($_POST['edit']) && !empty($_POST['edipass'])) {
      
    $edit = $_POST['edit'];
    $edipass = $_POST['edipass'];

    $id = $edit ; // idがこの値のデータだけを抽出したい、とする

    $sql = 'SELECT * FROM tbmm5_1 WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
    $stmt->execute();                             
    $result = $stmt->fetchAll(); 
	    foreach ($result as $rows){
      
        if ($edit == $rows{'id'} && $edipass == $rows{'password'}) {
          $editnumber = $rows{'id'};
          $editname = $rows{'name'};
          $editcomment = $rows{'comment'};
          $editpass = $rows{'password'};
          echo $rows{'id'}. "を編集中です。<br>";

        }elseif($edit == $rows{'id'} && $edipass !== $rows{'password'}){
          echo "パスワードが違います。<br>";
        }
      }
  }elseif(!empty($_POST['edit']) || !empty($_POST['edipass'])){
    echo "編集対象番号、パスワードを入力してください。<br>";
  }


//削除対象番号が空でないとき
  if (!empty($_POST['dnum'])  && !empty($_POST['delpass']) ) {
    
    $delete = $_POST['dnum'];
    $delpass = $_POST['delpass'];

    $id = $delete;  

    $sql = 'SELECT * FROM tbmm5_1 WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
    $stmt->execute();                             
    $result = $stmt->fetchAll(); 
      foreach ($result as $rowss){

        if ($delete == $rowss['id'] && $delpass == $rowss['password']) {
          $sql = 'delete from tbmm5_1 where id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          $stmt->execute();
          echo $rowss{'id'}. "を削除しました。<br>";
        }elseif($delete == $rowss['id'] && $delpass !== $rowss['password']) {
          echo "パスワードが違います。<br>";
        }  
      }
  }elseif(!empty($_POST['dnum']) || !empty($_POST['delpass']) ){
    echo "削除対象番号、パスワードを入力してください。<br>";
  }

?>     

<form action="m5-01.php" method="post">
      <input type="text" name="name" placeholder="名前" value="<?php if(isset($editname)) {echo $editname;}?>"><br>
      <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editcomment)) {echo $editcomment;} ?>"><br>
      <input type="hidden" name="editNO" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>">
      <input type="text" name="pass" placeholder="パスワード"value="<?php if(isset($editpass)) {echo $editpass;} ?>">
      <input type="submit" name="submit" value="送信">
    </form>

    <form action="m5-01.php" method="post">
      <input type="text" name="dnum" placeholder="削除対象番号"><br>
      <input type="text" name="delpass" placeholder="パスワード">
      <input type="submit" name="delete" value="削除">
    </form>

    <form action="m5-01.php" method="post">
      <input type="text" name="edit" placeholder="編集対象番号"><br>
      <input type="text" name="edipass" placeholder="パスワード">
      <input type="submit" value="編集">
    </form>

<?php
  //表示
  $sql = 'SELECT * FROM tbmm5_1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		
		echo $row['id'].',';
		echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['nichiji'].',';
		echo $row['password'].'<br>';
	echo "<hr>";
	}

?>
  </body>
</html>