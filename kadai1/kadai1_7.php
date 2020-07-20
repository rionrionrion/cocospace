<html>
<body>

<?php
  $file = "1_6.txt";
  $fp = fopen($file, "r");
  $i = 0;
  $my_array = array();
  while(!feof($fp)) {
    $my_array[$i] = fgets($fp);
    $i++;
  }
  foreach($my_array as $a) {
    echo $a;
    echo '<br>';
  }
?>

</body>
</html>
