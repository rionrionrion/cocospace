<?php
  session_start();
 ?>
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

  $sql = "DROP TABLE threadtable3";
  //$conn->query($sql);
  $sql = "DROP TABLE filetable3";
  //$conn->query($sql);

  // sql to create table
  $sql = "CREATE TABLE IF NOT EXISTS threadtable3 (
  id INTEGER(5) NOT NULL,
  userid INTEGER(9) NOT NULL,
  name varchar(20) NOT NULL,
  password varchar(20) NOT NULL,
  comment varchar(1000) NOT NULL,
  curr_date TIMESTAMP NOT NULL,
  fileid INTEGER(5)
  )";
  if ($conn->query($sql) === TRUE) {
    echo "<h3>ようこそ簡易掲示板へ！</h3>";
  } else {
    echo "エラーが発生しました： " . $conn->error;
  }

  //Creating a table storing paths of the images
  $sql = "CREATE TABLE IF NOT EXISTS filetable3 (
  id INTEGER(5) NOT NULL,
  name varchar(50) NOT NULL
  )";
  if ($conn->query($sql) === FALSE) {
    echo "エラーが発生しました： " . $conn->error;
  }

  #Retrieving number of elements:
  $sql = "SELECT COUNT(*) as c from threadtable3";
  $result = $conn->query($sql);
  $row = $result -> fetch_assoc();
  $count = $row["c"];

  #Retrieving number of images:
  $sql = "SELECT COUNT(*) as c from filetable3";
  $result = $conn->query($sql);
  $row = $result -> fetch_assoc();
  $count_file = $row["c"];

  #Display the name of the login user
  $loginid = -1;
  $loginname = "";
  $loginpassword = "";
  $loginmaster = -1;
  if(isset($_SESSION["id"])) {
    $loginid = (int) $_SESSION["id"];
    $sql = "SELECT id, name, password, master from usertable3 WHERE id=$loginid";
    $result = $conn->query($sql);
    $row= $result -> fetch_assoc();
    $loginid = $row["id"];
    $loginname = $row["name"];
    $loginpassword = $row["password"];
    $loginmaster = $row["master"];
    echo $loginname." さんとしてログイン中";
    echo "<br>";
  }

  #Initialize the parameters for editing
  $edit_flag = 0;
  $edit_id = 0;
  $edit_comment = "";

  #Checks which operation to perform
  if($_SERVER["REQUEST_METHOD"]=="POST") {
    #Case 1: Posting a comment
    if(isset($_POST["comment"])) {
      #Case 1a: Posting a new comment
      if($_POST["edit_flag"]==="0"){
        $comment = filter_input(INPUT_POST, 'comment');
        $curr_date = date('Y-m-d H:i:s');
        $id = $count + 1;
        $fileid = -1;
        $sql = "INSERT INTO threadtable3 (id, userid, name, password, comment, curr_date, fileid) VALUES ('$id', $loginid, '$loginname', '$loginpassword', '$comment', '$curr_date', '$fileid')";
        if ($conn->query($sql) === TRUE) {
          echo "コメントを投稿しました";
          echo "<br>";
        } else {
          echo "エラーが発生しました：" . $sql . "<br>" . $conn->error;
          echo "<br>";
        }
        //If there is a file uploaded
        if(isset($_FILES['file'])) {
          $file = $_FILES["file"]["name"];
          $file_ext = pathinfo($file)['extension'];
          //If the file extension is of an image
          if(in_array($file_ext, array("jpg", "jpeg", "JPG", "JPEG", "png", "gif"))) {
            $filename = basename($file);
            $target = "image3/".$filename;
            //Checks if there is already an image with the same filename, in which case it appends an integer at the back of the filename
            $sql = "SELECT * FROM filetable3 WHERE name='$target'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              $copy_filename = $filename;
              $i = 1;
              while(true) {
                $filename = basename($copy_filename, ".".$file_ext)."($i).".$file_ext;
                $target = "image3/".$filename;
                $sql = "SELECT name FROM filetable3 WHERE name='$target'";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                  $i++;
                }
                else {
                  break;
                }
              }
            }
            $target = "image3/".$filename;
            $file_id = $count_file + 1;
            $sql = "UPDATE threadtable3 SET fileid = '$file_id' WHERE id = '$id'";
            $conn->query($sql);
            $sql = "INSERT INTO filetable3 (id, name) VALUES ('$file_id', '$target')";
            $conn->query($sql);
            if(move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
              echo "画像をアップロードしました";
              echo $target;
            }
            else {
              echo "画像のアップロードに失敗しました";
            }
          }

          //If the file extension is of an video
          else if(in_array($file_ext, array("avi", "mp4", "mov", "mpeg"))) {
            $filename = basename($file);
            $target = "video3/".$filename;
            //Checks if there is already an image with the same filename, in which case it appends an integer at the back of the filename
            $sql = "SELECT * FROM filetable3 WHERE name='$target'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              $copy_filename = $filename;
              $i = 1;
              while(true) {
                $filename = basename($copy_filename, ".".$file_ext)."($i).".$file_ext;
                $target = "image3/".$filename;
                $sql = "SELECT name FROM filetable3 WHERE name='$target'";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                  $i++;
                }
                else {
                  break;
                }
              }
            }
            $target = "video3/".$filename;
            $file_id = $count_file + 1;
            $sql = "UPDATE threadtable3 SET fileid = '$file_id' WHERE id = '$id'";
            $conn->query($sql);
            $sql = "INSERT INTO filetable3 (id, name) VALUES ('$file_id', '$target')";
            $conn->query($sql);
            if(move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
              echo "動画をアップロードしました";
              echo $target;
            }
            else {
              echo "動画のアップロードに失敗しました";
            }
          }

          else {
            echo "そのファイル形式は使用できません。";
          }
          header("Location: thread.php");
          exit();
        }
      }

      #Case 1b: Editing an existing comment
      else if($_POST["edit_flag"]==="1") {
        $comment = filter_input(INPUT_POST, 'comment');
        $idx = filter_input(INPUT_POST, 'edit_id');
        $sql = "UPDATE threadtable3 SET comment = '$comment' WHERE id = $idx";
        if ($conn->query($sql) === TRUE) {
          echo "コメントが編集されました";
          echo "<br>";
        } else {
          echo "エラーが発生しました：" . $sql . "<br>" . $conn->error;
          echo "<br>";
        }
        $edit_flag = 0;
        //If there is a file uploaded
        if(isset($_FILES['file'])) {
          $sql = "SELECT fileid FROM threadtable3 WHERE id=$idx";
          $conn->query($sql);
          $row = $result -> fetch_assoc();
          if($row["fileid"]>0) {
            $id=$row["fileid"];
            $sqlfile = "SELECT name FROM filetable3 WHERE id='$id'";
            $resultfile = $conn->query($sqlfile);
            $rowfile = $resultfile->fetch_assoc();
            $src = $rowfile['name'];
            unlink($src);
            $sql = "DELETE FROM filetable3 WHERE id = '$id'";
            $result = $conn->query($sql);
          }
          $file = $_FILES["file"]["name"];
          $file_ext = pathinfo($file)['extension'];
          //If the file extension is of an image
          if(in_array($file_ext, array("jpg", "jpeg", "JPG", "JPEG", "png", "gif"))) {
            $filename = basename($file);
            $target = "image3/".$filename;
            //Checks if there is already an image with the same filename, in which case it appends an integer at the back of the filename
            $sql = "SELECT * FROM filetable3 WHERE name='$target'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              $copy_filename = $filename;
              $i = 1;
              while(true) {
                $filename = basename($copy_filename, ".".$file_ext)."($i).".$file_ext;
                $target = "image3/".$filename;
                $sql = "SELECT name FROM filetable3 WHERE name='$target'";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                  $i++;
                }
                else {
                  break;
                }
              }
            }
            $target = "image3/".$filename;
            $file_id = $count_file + 1;
            $sql = "UPDATE threadtable3 SET fileid = '$file_id' WHERE id = '$id'";
            $conn->query($sql);
            $sql = "INSERT INTO filetable3 (id, name) VALUES ('$file_id', '$target')";
            $conn->query($sql);
            if(move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
              echo "画像をアップロードしました";
              echo $target;
            }
            else {
              echo "画像のアップロードに失敗しました";
            }
          }

          //If the file extension is of an video
          else if(in_array($file_ext, array("avi", "mp4", "mov", "mpeg"))) {
            $filename = basename($file);
            $target = "video3/".$filename;
            //Checks if there is already an image with the same filename, in which case it appends an integer at the back of the filename
            $sql = "SELECT * FROM filetable3 WHERE name='$target'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              $copy_filename = $filename;
              $i = 1;
              while(true) {
                $filename = basename($copy_filename, ".".$file_ext)."($i).".$file_ext;
                $target = "image3/".$filename;
                $sql = "SELECT name FROM filetable3 WHERE name='$target'";
                $result = $conn->query($sql);
                if($result->num_rows > 0) {
                  $i++;
                }
                else {
                  break;
                }
              }
            }
            $target = "video3/".$filename;
            $file_id = $count_file + 1;
            $sql = "UPDATE threadtable3 SET fileid = '$file_id' WHERE id = '$idx'";
            $conn->query($sql);
            $sql = "INSERT INTO filetable3 (id, name) VALUES ('$file_id', '$target')";
            $conn->query($sql);
            if(move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
              echo "動画をアップロードしました";
              echo $target;
            }
            else {
              echo "動画のアップロードに失敗しました";
            }
          }

          else {
            echo "そのファイル形式は使用できません。";
          }
          header("Location: thread.php");
          exit();
        }
      }
    }

    #Case 2: Deleting a comment
    else if(isset($_POST["del_num"])) {
      $number = filter_input(INPUT_POST, 'del_num');
      if($number<=$count and $number>0) {
        $sql = "SELECT id, userid, fileid FROM threadtable3 WHERE id = $number";
        $result = $conn->query($sql);
        $row = $result -> fetch_assoc();

        if($loginid===$row["userid"]){
          if($row["fileid"]>0) {
            $id=$row["fileid"];
            $sqlfile = "SELECT name FROM filetable3 WHERE id='$id'";
            $resultfile = $conn->query($sqlfile);
            $rowfile = $resultfile->fetch_assoc();
            $src = $rowfile['name'];
            unlink($src);
            $sql = "DELETE FROM filetable3 WHERE id = '$id'";
            $result = $conn->query($sql);
          }
          $sql = "DELETE FROM threadtable3 WHERE id = $number";
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
            $sql = "UPDATE threadtable3 SET id = $number WHERE id = $idx";
            $result = $conn->query($sql);
            $number++;
          }
        }
        else {
          echo "他ユーザーのコメントは削除できません";
        }
      }
    }

    #Case 3: Validating the rights for editing
    else if(isset($_POST["edit_num"])) {
      $number = filter_input(INPUT_POST, 'edit_num');

      if($number<=$count and $number>0) {
        $sql = "SELECT id, userid FROM threadtable3 WHERE id = $number";
        $result = $conn->query($sql);
        $row = $result -> fetch_assoc();

        if($loginid===$row["userid"]){
          $sql = "SELECT id, comment FROM threadtable3 WHERE id=$number";
          $result = $conn->query($sql);
          $row = $result -> fetch_assoc();
          $edit_id = $row["id"];
          $edit_comment = $row["comment"];
          $edit_flag = 1;
          echo "<br>";
          echo "コメントを編集してください";
          echo "<br>";
        }
        else {
          echo "他ユーザーのコメントは編集できません";
        }
      }
    }
  }

  $sql = "SELECT id, name, comment, curr_date, fileid FROM threadtable3";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo "<br>";
      echo $row["id"]." ".$row["curr_date"]." 投稿主：".$row["name"]." ".$row["fileid"];
      echo "<br>";
      echo $row["comment"];
      echo "<br>";
      if($row['fileid']>0) {
        $id = $row['fileid'];
        $sqlfile = "SELECT name FROM filetable3 WHERE id='$id'";
        $resultfile = $conn->query($sqlfile);
        $rowfile = $resultfile->fetch_assoc();
        $src = $rowfile['name'];
        $filename = basename($src);
        $filetype = "video/".pathinfo($filename)['extension'];
        if(strcmp("image3", pathinfo($src)['dirname'])==0) {
          echo "<img src='$src' alt='$filename'><br>";
        }
        else if(strcmp("video3", pathinfo($src)['dirname'])==0) {
          echo "<video width='320' height='240' controls><source src='$src' type='video/mp4'></video>";
        }
      }
    }
  } else {
    echo "まだコメントが投稿されていません。";
  }

  $sql = "SELECT name FROM filetable3";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo "<br>";
      echo $row["name"];
    }
  } else {
    echo "まだファイルが投稿されていません。";
  }
  $conn->close();
  ?>

  <br>

  <form method="POST" enctype="multipart/form-data">
    コメント：
    <br>
    <input name="comment" type="text" value="<?= $edit_comment?>" required>
    <br>
    <input name="file" type="file">
    <br>
    <input type="hidden" value="<?= $edit_flag?>" name="edit_flag">
    <input type="hidden" value="<?= $edit_id;?>" name="edit_id">
    <input type="submit" value="投稿する">

  </form>

  <form method="POST" onsubmit="return confirm_del()">
    削除番号指定用フォーム：
    <br>
    <input name="del_num" type="number" required>
    <br>
    <input type="submit" value="削除">
  </form>

  <form method="POST">
    編集番号指定用フォーム：
    <br>
    <input name="edit_num" type="number" required>
    <br>
    <input type="submit" value="編集">
  </form>

  <form action="login.php" method="POST">
    <input type="submit" value="ログアウト">
  </form>
  <form action="register.php" method="POST">
    <input type="submit" value="ID新規登録画面へ">
  </form>
  <form action="master.php" method="POST">
    <input type="submit" value="管理者画面（仮）">
  </form>


</body>
</html>
