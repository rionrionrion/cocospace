<?php
  session_start();
 ?>
<html>
<head>
  <title>簡易掲示板　ログインページ</title>
</head>
<body>
  <h3>ログインページ</h3>
  <?php

  #Initializing the login user
  if (isset($_SESSION["id"])) {
    unset ($_SESSION["id"]);
  }

  #MYSQL connection
  $servername = "localhost";
  $username = "co-19-225.99sv-c";
  $password = "Nq3y7khg";
  $dbname = "co_19_225_99sv_coco_com";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("接続に失敗しました: " . $conn->connect_error);
  }

  if($_SERVER["REQUEST_METHOD"]=="POST"){

    if (isset($_POST["name"])) {
      $name = filter_input(INPUT_POST, 'name');
      $password = filter_input(INPUT_POST, 'password');

      $sql = "SELECT id, name, password, master FROM usertable3 WHERE name='$name'";
      $result = $conn->query($sql);
      if($result->num_rows > 0) {

        #Reroute if the password match to the thread
        $row = $result->fetch_assoc();
        if(strcmp($password, $row["password"])==0) {
          if($row["master"]==="1") {
            $_SESSION["id"] = $row["id"];
            header("Location: master.php");
            exit;
          }
          else {
            $_SESSION["id"] = $row["id"];
            header("Location: thread.php");
            exit;
          }
        }

        #The password doesn't match
        else {
          echo "ユーザー名かパスワードが間違っています";
          echo "<br>";
        }

      }
      #The user doesn't exist
      else {
        echo "ユーザー名かパスワードが間違っています";
        echo "<br>";
      }
    }
  }
   ?>
  <form method="POST">
    ユーザー名：
    <br>
    <input type="text" name="name" required>
    <br>
    パスワード：
    <br>
    <input type="password" name="password" required>
    <br>
    <input type="submit" value="ログイン">
  </form>
  <br>
  <form action="register.php" method="POST">
    <input type="submit" value="ID新規登録画面へ">
  </form>
  <form action="master.php" method="POST">
    <input type="submit" value="管理者画面（仮）">
  </form>
</body>
</html>
