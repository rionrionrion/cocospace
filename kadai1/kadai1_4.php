<html>
<body>
  <form method="POST">
    文字列を入力してください：
    <input type="text" name="text">
    <input type="submit" value="submit">
  </form>
  <?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      echo $_POST["text"];
    }

   ?>
</body>
</html>
