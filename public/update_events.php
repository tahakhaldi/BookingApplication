<?php

require "../config.php";

try {
    $connection = new PDO($dsn, $username, $password, $options);
    
    $event =[
        "id"        => $_POST['id'],
        "title" => $_POST['title'],
        "start"  => $_POST['start'],
        "end"     => $_POST['end']
    ];
    
    $sql = "UPDATE bookings
            SET title = :title,
            start = :start,
            end = :end
            WHERE id = :id";
    
    $statement = $connection->prepare($sql);
    $statement->execute($event);
} catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
}

?>