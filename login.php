<?php
include_once("connection.php");
echo $_POST["username"]."<br>";
echo $_POST["passwd"]."<br>";
print_r($_POST);

$stmt = $conn->prepare("INSERT INTO student
(studentID, username, password, totalBalance, diamondBalance)VALUES
(null,:username,:password)");

$stmt -> bindParam(':username', $_POST["username"]);
$stmt -> bindParam(':password',$_POST["passwd"]);
$stmt->execute();
$conn=null;
?>