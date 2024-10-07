<?php
include_once("connection.php");
echo $_POST["username"]."<br>";
echo $_POST["passwd"]."<br>";
print_r($_POST);

$stmt = $conn->prepare("INSERT INTO student
(username, `password`)
VALUES (:username,:password)");

$stmt -> bindParam(':username', $_POST["username"]);
$stmt -> bindParam(':password',$_POST["passwd"]);
$stmt->execute();
//send a success message to the session
$_SESSION['message'] = 'You have successfully signed up.';
$conn=null;

//redirect user to the public page
header('Location: publicPage.php');
?>