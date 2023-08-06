<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'dbConnection.php';

$id = $_POST['id'];
$password = $_POST['password'];

// Check if the user exists in the 'drivers' table
$checkUserQueryDrivers = "SELECT CONCAT(d_prefix, d_id) AS user_id, name, email FROM driver WHERE CONCAT(d_prefix, d_id) = '$id' AND password = '$password'";
$resultDrivers = mysqli_query($con, $checkUserQueryDrivers);

// Check for query execution errors
if (!$resultDrivers) {
    die('Query Execution Error: ' . mysqli_error($con));
}

if (mysqli_num_rows($resultDrivers) > 0) {
    // User exists in the 'drivers' table
    $row = mysqli_fetch_assoc($resultDrivers);
    $response['user'] = $row;
    $response['user']['user_type'] = 'driver';
} else {
    // Check if the user exists in the 'parents' table
    $checkUserQueryParents = "SELECT CONCAT(prefix, id) AS user_id, name, email FROM parents WHERE CONCAT(prefix, id) = '$id' AND password = '$password'";
    $resultParents = mysqli_query($con, $checkUserQueryParents);

    // Check for query execution errors
    if (!$resultParents) {
        die('Query Execution Error: ' . mysqli_error($con));
    }

    // If user exists in the 'parents' table
    if (mysqli_num_rows($resultParents) > 0) {
        $row = mysqli_fetch_assoc($resultParents);
        $response['user'] = $row;
        $response['user']['user_type'] = 'parent';
    } else {
        // User does not exist in either table
        $response['user']['user_id'] = ''; // Set an empty value for 'user_id'
        $response['user']['name'] = '';
        $response['user']['email'] = '';
        $response['user']['user_type'] = ''; // Set an empty value for 'user_type'
    }
}

// Check for the correct password and set the response accordingly
if (!empty($response['user']['user_id'])) {
    $response['error'] = "200";
    $response['message'] = "Login success";
} else {
    $response['error'] = "400";
    $response['message'] = "Wrong credentials";
}

echo json_encode($response);

?>
