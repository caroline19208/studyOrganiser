<?php
session_start();

include_once("connection.php");

try {
    // Sanitize the input and hash the password
    array_map("htmlspecialchars", $_POST);
    $username = $_POST['username'];
    $password = password_hash($_POST["passwd"], PASSWORD_DEFAULT);

    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO student 
        (username, `password`) 
        VALUES (:username, :password)");

    // Bind parameters to the query
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    // Execute the query
    $stmt->execute();

    // Send a success message to the session
    $_SESSION['message'] = 'You have successfully signed up.';

    // Close the connection
    $conn = null;

    // Redirect the user to the public page
    header('Location: publicPage.php');
    exit(); // Always exit after redirect
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
