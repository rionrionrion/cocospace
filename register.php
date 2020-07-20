<html>
<head>
  <title>簡易掲示板　新規登録ページ</title>
</head>
<body>
  <h3>新規ユーザー登録ページ</h3>
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
    die("接続に失敗しました: " . $conn->connect_error);
  }

  #Retrieving number of users:
  $sql = "SELECT MAX(id) as c from usertable3";
  $result = $conn->query($sql);
  $n = $result -> fetch_assoc();
  $count = $n["c"];

  #Adding a user:
  if($_SERVER["REQUEST_METHOD"]=="POST"){

    if(isset($_POST["name"])) {

      $id = $count + 1;
      $name = filter_input(INPUT_POST, 'name');
      $password = filter_input(INPUT_POST, 'password');
      $check_password = filter_input(INPUT_POST, 'check_password');
      $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $flag = True;

      #Check the length of the input
      if (strlen($name) > 20) {
        echo "ユーザー名が長すぎます。２０文字以内で設定してください";
        echo "<br>";
        $flag = False;
      }

      if (strlen($password) > 20) {
        echo "パスワードが長すぎます。２０文字以内で設定してください";
        echo "<br>";
        $flag = False;
      }

      if (strlen($email) > 50) {
        echo "メールアドレスが長すぎます。";
        echo "<br>";
        $flag = False;
      }

      #Check if the password matches with the check_password
      if (strcmp($password, $check_password)!==0) {
        echo "パスワードが一致しません";
        echo "<br>";
        $flag = False;
      }

      #Check if the username already exists
      $sql = "SELECT name FROM usertable3 WHERE name='$name'";
      $result = $conn->query($sql);
      if($result->num_rows > 0) {
        echo "ユーザー名は既に使用されています";
        echo "<br>";
        $flag = False;
      }

      #Check if the email already exists
      $sql = "SELECT email FROM usertable3 WHERE email='$email'";
      $result = $conn->query($sql);
      if($result->num_rows > 0) {
        echo "メールアドレスは既に使用されています";
        echo "<br>";
        $flag = False;
      }

      #Successful registration
      if($flag) {
        $subject = "簡易掲示板本登録用メール";
        $message = "以下のリンクをクリックして本登録をお済ませください";
        $header = "From: cocothread3@outlook.com";
        if(mail($email, $subject, $message, $header)) {
          $sql = "INSERT INTO usertable3 (id, name, password, email, master, flag) VALUES ('$id', '$name', '$password', '$email', 0, 0)";
          $conn->query($sql);
          echo "仮登録を完了しました。２４時間以内に認証メールから本登録をお済ませください。";
          echo "<br>";
        }
      }
      else {
        echo "エラーが発生しました：" . $sql . "<br>" . $conn->error;
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
    メールアドレス：
    <br>
    <input type="text" name="email" required>
    <br>
    パスワード：
    <br>
    <input type="password" name="password" required>
    <br>
    パスワード再入力：
    <br>
    <input type="password" name="check_password" required>
    <br>
    <input type="submit" value="送信">
  </form>
  <br>
  <form action="login.php" method="POST">
    <input type="submit" value="ログイン画面へ戻る">
  </form>
  <form action="master.php" method="POST">
    <input type="submit" value="管理者画面（仮）">
  </form>
</body>
</html>
