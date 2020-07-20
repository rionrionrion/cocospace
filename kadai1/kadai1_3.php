<?php
  $textname = "1_2.txt";
  readfile($textname);

  <form action="" method="POST">
    <p>好きな文字列を入力してください<input type="text" name="firsttext"></p>
    <input type="submit">
  </form>
  <?= $_POST["firsttext"];?>
