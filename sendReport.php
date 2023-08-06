<?php
header('Content-Type: application/json');

require 'dbConnection.php';

$enteredLoginID = $_POST['p_id']; // The user-provided login ID, e.g., "AHP12"
$report_type = $_POST['report_type'];
$parent_status = $_POST['parent_status'];
$content = $_POST['content'];

// Check if the parent exists in the parents table based on the provided login ID
$parentExists = false;
if (!empty($enteredLoginID)) {
    // Extract prefix and p_id from the entered login ID
    $prefix = substr($enteredLoginID, 0, 3); // Assuming prefix is the first three characters
    $p_id = substr($enteredLoginID, 3); // Assuming p_id is the rest of the characters

    // Check if the extracted p_id and prefix exist in the parents table
    $checkParentQuery = "SELECT 1 FROM parents WHERE id = '$p_id' AND prefix = '$prefix'";
    $resultParent = mysqli_query($con, $checkParentQuery);

    // Handle query execution error
    if (!$resultParent) {
        $response['error'] = "500";
        $response['message'] = "Database Error: " . mysqli_error($con);
        echo json_encode($response);
        exit; // Exit the script to prevent further processing
    }

    $parentExists = (mysqli_num_rows($resultParent) > 0);
}

// If parent does not exist, reject report submission
if (!$parentExists) {
    $response['error'] = "403";
    $response['message'] = "Unauthorized User";
    echo json_encode($response);
    exit; // Exit the script to prevent further processing
}

if(strcasecmp($report_type, "report") === 0){
    // If parent_status is "with credentials," use the entered login ID and prefix
    if (strcasecmp($parent_status, "with credentials") === 0) {
        $insertQuery = "INSERT INTO report(p_id, prefix, report_content) 
                        VALUES ('$p_id', '$prefix', '$content')";
    } 
    else {
    // If parent_status is "anonymous" or not provided, use NULL as p_id and prefix for anonymous report submission
    $insertQuery = "INSERT INTO report(p_id, prefix, report_content) 
                    VALUES (NULL, NULL, '$content')";
    }
}
else{
    $insertQuery = "INSERT INTO `leave`(p_id, prefix, leave_content) 
                    VALUES ('$p_id', '$prefix', '$content')";
}
$result = mysqli_query($con, $insertQuery);

$response = array();

if ($result) {
    $response['error'] = "200";
    $response['message'] = "Request Sent";
} else {
    $response['error'] = "500";
    $response['message'] = "Database Error: " . mysqli_error($con);
}

echo json_encode($response);
?>
