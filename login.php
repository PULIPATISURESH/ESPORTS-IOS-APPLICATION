<?php
//error_reporting(0);

include 'db.php';
$email = $_GET['email'];
$password = $_GET['password'];

$loginqry = "SELECT * FROM sign WHERE email = '$email' AND password = '$password'";
$qry = mysqli_query($conn, $loginqry);

$response = array();

if (!$qry) {
    $response['status'] = false;
    $response['message'] = "Query failed: " . mysqli_error($conn);
} else {
    if (mysqli_num_rows($qry) > 0) {
        $userObj = mysqli_fetch_assoc($qry);
        $response['status'] = true;
        $response['message'] = "Login Successfully";
        $response['data'] = [$userObj]; // Wrap the userObj in square brackets
    } else {
        $response['status'] = false;
        $response['message'] = "Login Failed";
    }
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response);
?>
