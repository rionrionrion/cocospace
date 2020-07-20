<html>

<head>
  <title>簡易掲示板</title>
</head>

<body>
<?php
#MYSQL connection
$servername = "localhost";
$username = "co-19-225.99sv-c";
$password = "Nq3y7khg";
$dbname = "co_19_225_99sv_coco_com";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE IF NOT EXISTS threadtable (
id INTEGER(5) NOT NULL,
name varchar(64) NOT NULL,
password varchar(64) NOT NULL,
comment varchar(1000) NOT NULL,
curr_date TIMESTAMP NOT NULL
)";
if ($conn->query($sql) === TRUE) {
  echo "ようこそ簡易掲示板へ！";
  echo "<br>";
} else {
  echo "エラーが発生しました： " . $conn->error;
}

#Initialize the parameters for editing
$edit_flag = 0;
$edit_id = 0;
$edit_val = "";
$edit_name = "";
$edit_pass = "";

#Retrieving number of elements:
$sql = "SELECT COUNT(*) as c from threadtable";
$result = $conn->query($sql);
$n = $result -> fetch_assoc();
$count = $n["c"];

#Checks which operation to perform
if($_SERVER["REQUEST_METHOD"]=="POST") {
  #Case 1: Posting a comment
  if(isset($_POST["name"])) {
    #Case 1a: Posting a new comment
    if($_POST["edit_flag"]==="0"){
      $name = filter_input(INPUT_POST, 'name');
      $password = filter_input(INPUT_POST, 'password');
      $comment = filter_input(INPUT_POST, 'comment');
      $curr_date = date('Y-m-d H:i:s');
      $id = $count + 1;
      $sql = "INSERT INTO threadtable (id, name, password, comment, curr_date) VALUES ('$id', '$name', '$password', '$comment', '$curr_date')";
      if ($conn->query($sql) === TRUE) {
        echo "コメントを投稿しました";
        echo "<br>";
      } else {
        echo "エラーが発生しました：" . $sql . "<br>" . $conn->error;
        echo "<br>";
      }
    }
    #Case 1b: Editing an existing comment
    else if($_POST["edit_flag"]==="1") {
      $comment = filter_input(INPUT_POST, 'comment');
      $idx = filter_input(INPUT_POST, 'edit_id');
      $sql = "UPDATE threadtable SET comment = '$comment' WHERE id = $idx";
      if ($conn->query($sql) === TRUE) {
        echo "コメントが編集されました";
        echo "<br>";
      } else {
        echo "エラーが発生しました：" . $sql . "<br>" . $conn->error;
        echo "<br>";
      }
    }

  }
  #Case 2: Deleting a comment
  else if(isset($_POST["del_num"])) {
    $number = filter_input(INPUT_POST, 'del_num');
    if($number<=$count and $number>0) {
      $sql = "SELECT password as p FROM threadtable WHERE id = $number";
      $result = $conn->query($sql);
      $n = $result -> fetch_assoc();
      $pass_idx = $n["p"];
      if(strcmp($pass_idx, $_POST["password_del"])==0){
        $sql = "DELETE FROM threadtable WHERE id = $number";
        if ($conn->query($sql) === TRUE) {
          echo "<br>";
          echo "コメントを削除しました。";
          echo "<br>";
        } else {
          echo "エラーが発生しました：" . $sql . "<br>" . $conn->error;
          echo "<br>";
        }
        while($number<$count) {
          $idx = $number + 1;
          $sql = "UPDATE threadtable SET id = $number WHERE id = $idx";
          $result = $conn->query($sql);
          $number++;
        }
      }
      else {
        echo "パスワードが間違っています";
      }
    }
  }
  #Case 3: Validating the rights for editing
  else if(isset($_POST["edit_num"])) {
    $number = filter_input(INPUT_POST, 'edit_num');
    if($number<=$count and $number>0) {
      $sql = "SELECT password as p FROM threadtable WHERE id = $number";
      $result = $conn->query($sql);
      $n = $result -> fetch_assoc();
      $pass_idx = $n["p"];
      if(strcmp($pass_idx, $_POST["password_edit"])==0){
        $sql = "SELECT id, name, comment, password FROM threadtable WHERE id=$number";
        $result = $conn->query($sql);
        $n = $result -> fetch_assoc();
        $edit_val = $n["comment"];
        $edit_name = $n["name"];
        $edit_pass = $n["password"];
        $edit_flag = 1;
        $edit_id = $n["id"];
        echo "<br>";
        echo "コメントを編集してください";
        echo "<br>";
      }
      else {
        echo "パスワードが間違っています";
      }
    }
  }
}

$sql = "SELECT id, name, comment, curr_date, password FROM threadtable";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "<br>";
    echo $row["id"]." ".$row["curr_date"]." 投稿主：".$row["name"];
    echo "<br>";
    echo $row["comment"];
    echo "<br>";
  }
} else {
  echo "まだコメントが投稿されていません。";
}
$conn->close();
 ?>

 <script>
 function confirm_del() {
   return confirm("コメントを削除します。本当によろしいですか？")
 }
 </script>

 <form method="POST">
   <br>
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
   <input type="hidden" value="<?= $edit_id;?>" name="edit_id">
   <input type="submit" value="投稿">
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
   <input type="submit" value="削除">
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
   <input type="submit" value="編集">
 </form>
</body>
</html>
