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
    if (isset($input_arrays[$i]['firstname'])){
        $output_arrays[$i]['title'] = $input_arrays[$i]['firstname'];
        $output_arrays[$i]['firstname'] = $input_arrays[$i]['firstname'];
    }
    if (isset($input_arrays[$i]['lastname'])){
        $output_arrays[$i]['lastname'] = $input_arrays[$i]['lastname'];
    }
    if (isset($input_arrays[$i]['reason'])){
        $output_arrays[$i]['reason'] = $input_arrays[$i]['reason'];
    }
    if (isset($input_arrays[$i]['age'])){
        $output_arrays[$i]['age'] = (new DateTime($input_arrays[$i]['age']))->format('Y-m-d');
    }
    if (isset($input_arrays[$i]['physician'])){
        $output_arrays[$i]['physician'] = $input_arrays[$i]['physician'];
    }
    if (isset($input_arrays[$i]['gender'])){
        $output_arrays[$i]['gender'] = $input_arrays[$i]['gender'];
    }
    if (isset($input_arrays[$i]['approval']) && ($input_arrays[$i]['approval'] == '1')){
        $output_arrays[$i]['color'] = "#31CD73";
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