<html>
<head>
  <title>簡易掲示板</title>
</head>
<body>
  <?php
    $edit_flag = 0;
    $edit_comment;
    $edit_val = "";
    $edit_name = "";
    $edit_pass = "";
    $file_name = "thread.txt";
    $fp = fopen($file_name, "a");
    fclose($fp);
    $number = 1;
    if($_SERVER["REQUEST_METHOD"]=="POST") {
      if(isset($_POST["name"])){
        if($_POST["edit_flag"]==="0") {
          echo "コメントが投稿されました";
          $fp = fopen($file_name, "r");
          while(($data = fgets($fp))!==false){
            $parts = explode("<>",$data);
            $number = $parts[0]+1;
          }
          fclose($fp);
          $fp = fopen($file_name, "a");
          $text = $number."<>".$_POST["name"]."<>".$_POST["comment"]."<>".date("c")."<>".$_POST["password"]."<>"."\n";
          fwrite($fp, $text);
          fclose($fp);
          $edit_flag = 0;
        }
        else if($_POST["edit_flag"]==="1"){
          echo "コメントが編集されました";
          $fp = fopen($file_name, "r");
          $array = array();
          $edit_comment = explode("<>", $_POST["edit_comment"]);
          while(($data = fgets($fp))!==false){
            $parts = explode("<>",$data);
            if($parts[0]!==$edit_comment[0]) {
              array_push($array, $data);
            }
            else {
              $text = $edit_comment[0]."<>".$edit_comment[1]."<>".$_POST["comment"]."<>".$edit_comment[3]."<>".$edit_comment[4];
              array_push($array, $text);
            }
          }
          fclose($fp);
          $fp = fopen($file_name, "w");
          foreach($array as $a){
            fwrite($fp, $a);
          }
          fclose($fp);
          $edit_flag = 0;
        }
      }
      if(isset($_POST["del_num"])) {
        $fp = fopen($file_name, "r");
        $array = array();
        while(($data = fgets($fp))!=false){
          $parts = explode("<>",$data);
          if($parts[0]!==$_POST["del_num"]) {
            array_push($array, $data);
          }
          else if(strcmp($parts[4], $_POST["password_del"])==0){
            echo "コメントが削除されました";
          }
          else {
            array_push($array, $data);
            echo "投稿番号またはパスワードが間違っています";
          }
        }
        fclose($fp);
        $fp = fopen($file_name, "w");
        foreach($array as $a){
          fwrite($fp, $a);
        }
        fclose($fp);
        $edit_flag = 0;
      }
      if(isset($_POST["edit_num"])) {
        $fp = fopen($file_name, "r");
        while(($data = fgets($fp))!==false){
          $parts = explode("<>",$data);
          if($parts[0]===$_POST["edit_num"]&&strcmp($_POST["password_edit"], $_POST["password_edit"])==0) {
            echo "コメントを編集してください";
            $edit_val = $parts[2];
            $edit_name = $parts[1];
            $edit_pass = $parts[4];
            $edit_flag = 1;
            $edit_comment = $data;
            break;
          }
          echo "投稿番号またはパスワードが間違っています";
        }
      }
    }
    $fp = fopen($file_name, "r");
    while(($data = fgets($fp))!==false){
      $parts = explode("<>",$data);
      echo "<br>";
      echo $parts[0]." 名前：".$parts[1]." 日付：".$parts[3];
      echo "<br>";
      echo "本文：".$parts[2];
      echo "<br>";
    }
    fclose($fp);

   ?>

  <script>
  function confirm_del() {
    return confirm("コメントを削除します。本当によろしいですか？")
  }
  </script>

  <form method="POST">
    <p>コメント</p>
    名前：
    <br>
    <input name="name" type="text" value="<?= $edit_name?>" required>
    <br>
    本文：
    <br>
    <input name="comment" type="text" value="<?= $edit_val?>" required>
    <br>
    パスワード：
    <br>
    <input name="password" type="password" value="<?= $edit_pass?>" required>
    <br>
    <input type="hidden" value="<?= $edit_flag?>" name="edit_flag">
    <input type="hidden" value="<?= $edit_comment;?>" name="edit_comment">
    <input type="submit" value="submit">
  </form>
  <form method="POST" onsubmit="return confirm_del()">
    <p>削除番号指定用フォーム</p>
    番号：
    <br>
    <input name="del_num" type="number" required>
    <br>
    パスワード：
    <br>
    <input name="password_del" type="password" required>
    <br>
    <input type="submit" value="submit">
  </form>
  <form method="POST">
    <p>編集番号指定用フォーム</p>
    番号：
    <br>
    <input name="edit_num" type="number" required>
    <br>
    パスワード：
    <br>
    <input name="password_edit" type="password" required>
    <br>
    <input type="submit" value="submit">
  </form>

</body>
</html>
