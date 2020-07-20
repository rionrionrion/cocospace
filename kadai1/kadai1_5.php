<html>
<body>
  <form method="POST">
    文字列を入力してください：
    <input type="text" name="text">
    <input type="submit" value="submit">
  </form>
  <?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      $name = "1_5.txt";
      $fp = fopen($name, "w");
      $text = $_POST["text"];
      fwrite($fp, $text);
      echo $text." has been written on the file kadai1_5.txt";
      fclose($fp);
      readfile($name);
    }

   ?>
</body>
</html>
