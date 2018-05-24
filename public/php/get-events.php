<?php

// Require datetime utilities
require dirname(__FILE__) . '/utils.php';

require "../../config.php";

try  {
    $connection = new PDO($dsn, $username, $password, $options);
    
    $sql = "SELECT * FROM bookings ORDER BY id";
    
    $statement = $connection->prepare($sql);
    $statement->execute();
    
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    $json = json_encode($result);
} catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
}

$input_arrays = json_decode($json, true);

// Accumulate an output array of event data arrays.
$output_arrays = array();
for ($i = 0; $i < count($input_arrays); $i++) {
    if(isset($input_arrays[$i]['id'])){
        $output_arrays[$i]['id'] = $input_arrays[$i]['id'];
    }
    if (isset($input_arrays[$i]['title'])){
        $output_arrays[$i]['title'] = $input_arrays[$i]['title'];
    }
    if (isset($input_arrays[$i]['approval']) && ($input_arrays[$i]['approval'] == '1')){
        $output_arrays[$i]['color'] = "green";
    }
    if (isset($input_arrays[$i]['start'])){
        $output_arrays[$i]['start'] = (new DateTime($input_arrays[$i]['start']))->format('Y-m-d H:i:s');
    }
    if (isset($input_arrays[$i]['end'])){
        $output_arrays[$i]['end'] = (new DateTime($input_arrays[$i]['end']))->format('Y-m-d H:i:s');
    }
}

// Send JSON to the client.
echo json_encode($output_arrays);