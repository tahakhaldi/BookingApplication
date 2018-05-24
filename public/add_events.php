<?php

require "../config.php";

try  {
    $connection = new PDO($dsn, $username, $password, $options);
    
    $new_event = array(
        "title" => $_POST['title'],
        "start"  => $_POST['start'],
        "end"     => $_POST['end']
    );
    
    $sql = "INSERT INTO bookings (title, start, end) VALUES (:title, :start, :end )";
    
    $statement = $connection->prepare($sql);
    $statement->execute($new_event);
} catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
}


?>