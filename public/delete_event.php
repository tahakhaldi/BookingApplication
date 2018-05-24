<?php

require "../config.php";

try {
    $connection = new PDO($dsn, $username, $password, $options);
    
    $id = $_POST["id"];
    
    $sql = "DELETE FROM bookings WHERE id = :id";
    
    $statement = $connection->prepare($sql);
    $statement->bindValue(':id', $id);
    $statement->execute();
    
    $success = "User successfully deleted";
} catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
}

?>
