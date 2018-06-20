<?php

require "../config.php";

try {
    $connection = new PDO($dsn, $username, $password, $options);
    
    $id = $_POST["id"];
    
    $sql = "UPDATE bookings
            SET approval = '1'
            WHERE id = :id";
    
    $statement = $connection->prepare($sql);
    $statement->bindValue(':id', $id);
    $statement->execute($event);

} catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
}

?>
