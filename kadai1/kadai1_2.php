<?php
  $name = "1_2.txt";
  $fp = fopen($name, "w");
  $text = "cocospace";
  fwrite($fp, $text);
  echo $text." has been written on the file kadai1_2.txt";
  fclose($fp);
  readfile($name);
