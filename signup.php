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
    // Check if the error is due to a duplicate entry for the username
    if ($e->getCode() == 23000) {  // Error code 23000 is for unique constraint violation
        $_SESSION['message'] = 'Username already taken. Please choose another one.';
        header('Location: signup.php');  // Redirect back to signup page
        exit();
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
