<?php
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
$sql = "CREATE TABLE IF NOT EXISTS testtable (
name varchar(64) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
  echo "Table MyGuests created successfully";
} else {
  echo "Error creating table: " . $conn->error;
}

$name = filter_input(INPUT_POST, 'name');
$sql = "INSERT INTO testtable (name) VALUES ('$name')";

if ($conn->query($sql) === TRUE) {
  echo "Name added successfully";
} else {
  echo "Error" . $sql . "<br>" . $conn->error;
}

$sql = "SELECT name FROM testtable";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["name"];
  }
} else {
  echo "0 results";
}
$conn->close();
?>

<html>
<head>
</head>
<body>
  <form method="POST">
    <input name="name" type="test">
    <input type="submit" value="submit">
   </form>
</body>
</html>
