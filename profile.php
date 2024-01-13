<?php

include 'db.php';

$userid =$_GET['userid'];

$loginqry = "SELECT * FROM sign WHERE userid = '$userid'";
$qry = mysqli_query($conn, $loginqry);
if(mysqli_num_rows($qry) > 0){
$userObj = mysqli_fetch_assoc($qry);
$response['status'] = true;
$response['message']= " Login Successfully";
$response['data'] = $userObj;
}
else{
$response['status'] = false;
$response['message']= "Login Failed";
}
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response);
?>