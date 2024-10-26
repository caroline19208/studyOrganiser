<?php
session_start();
if (!isset($_SESSION['studentID'])) {
    //redirect to public page if studentID not set
    header("Location: publicPage.php"); 
    exit();
}
?>

<?php include 'navbar.php'; ?>
