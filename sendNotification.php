<?php

require 'dbConnection.php';
$notification_content = $_POST['notification_content'];
$date = $_POST['date'];
$time = $_POST['time'];

$insertNotif = "INSERT INTO notification (notification_content, Date, time) VALUES ('$notification_content', '$date', '$time')";
$resultNotifInsertion = mysqli_query($con, $insertNotif);

$response = array();

if ($resultNotifInsertion) {
    $response['error'] = "200";
    $response['message'] = "Request Sent";
} else {
    $response['error'] = "500";
    $response['message'] = "Database Error: " . mysqli_error($con);
}

echo json_encode($response);

?>
