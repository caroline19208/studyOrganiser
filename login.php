<?php
session_start();  // Start session

include_once("connection.php");

try {
    // Check if form data exists
    if (isset($_POST['username']) && isset($_POST['passwd'])) {

        // Sanitize all inputs from the POST array
        $_POST = array_map("htmlspecialchars", $_POST);

        // Prepare the SQL statement to find the user by username
        $stmt = $conn->prepare("SELECT * FROM student WHERE username = :username");
        $stmt->bindParam(':username', $_POST['username']);
        $stmt->execute();

        // Fetch the user data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) { 
            if (password_verify($_POST['passwd'], $row['password'])) {
                header('Location: schedulePage.php');
                exit();
            } else {
                $_SESSION['message'] = 'Invalid credentials. Please try again.';
                header('Location: publicPage.php');
                exit();
            }
        } else {
            $_SESSION['message'] = 'User not found. Please try again.';
            header('Location: publicPage.php');
            exit();
        }
    } else {
        header('Location: publicPage.php');
        exit();
    }

} catch (PDOException $e) {
    // If there's an error with the database, show it
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>
