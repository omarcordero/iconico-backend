<?php
  try {
    $conn = new PDO("mysql:host=localhost:3308;dbname=delivery", "admin", "admin");

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Connected to the database.<br>';

    $sql = 'SELECT name FROM users';
    
    print "Employee Name:<br>";
    foreach ($conn->query($sql) as $row) {
        print $row['name'] . "<br>";
    }
    $conn = null;

  }
  catch(PDOException $err) {
    echo "ERROR: Unable to connect: " . $err->getMessage();
  }
?>