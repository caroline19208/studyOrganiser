<?php
session_start();
include_once("connection.php");
include("navbar.php");

// Check if student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: publicPage.php"); 
    exit();
}

// Get the topicID from the URL
$topicID = $_GET['topicID'];
$studentID = $_SESSION['studentID'];

// Fetch topic details
$stmt = $conn->prepare("SELECT * FROM TOPIC WHERE topicID = ? AND studentID = ?");
$stmt->execute([$topicID, $studentID]);
$topic = $stmt->fetch();

if (!$topic) {
    echo "Topic not found or you don't have access.";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($topic['topicName']); ?></title>
</head>
<body>
    <button onclick="window.location.href='subjectPage.php?subjectID=<?php echo htmlspecialchars($topic['subjectID']); ?>';">Back to Topics List</button>
    <h2><?php echo htmlspecialchars($topic['topicName']); ?></h2>
    <p>Details about learning objectives</p>
</body>
</html>
