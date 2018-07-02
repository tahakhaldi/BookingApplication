<?php

require "../config.php";

try {
    $connection = new PDO($dsn, $username, $password, $options);
    
    $selectsql = "SELECT * FROM bookings WHERE id = :id";  
    $statement = $connection->prepare($selectsql);
    $statement->bindParam(':id',$_POST['id'], PDO::PARAM_STR);
    $statement->execute();
    
    $event = [      
        "id" => $_POST['id'],
        "firstname" => $_POST['firstname'],
        "lastname" => $_POST['lastname'],
        "reason" => $_POST['reason'],
        "physician" => $_POST['physician'],
        "age" => $_POST['age'],
        "gender" => $_POST['gender'],
        "start" => $_POST['start'],
        "end" => $_POST['end']
    ];
    
    if ($statement->rowCount() > 0) {
        $sql = "UPDATE bookings
            SET firstname = :firstname,
            lastname = :lastname,
            reason = :reason,
            physician = :physician,
            age = :age,
            gender = :gender,
            start = :start,
            end = :end,
            last_updated = CURRENT_TIMESTAMP
            WHERE id = :id";
    } else {
        unset($event["id"]);
        $sql = "INSERT INTO bookings 
            (firstname, lastname, reason, physician, age, gender, start, end) 
            VALUES (:firstname, :lastname, :reason, :physician, :age, :gender, :start, :end)";
    }    
    
    $statement = $connection->prepare($sql);
    $statement->execute($event);
    
} catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
}

?>