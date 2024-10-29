<?php
session_start();
include_once("connection.php");
include("navbar.php");

if (!isset($_SESSION['studentID'])) {
    header("Location: publicPage.php");
    exit();
}

$studentID = $_SESSION['studentID'];
$objectiveID = $_GET['objectiveID'] ?? null;

// Fetch learning objective details
$stmt = $conn->prepare("SELECT * FROM LEARNING_OBJECTIVE WHERE objectiveID = ? AND studentID = ?");
$stmt->execute([$objectiveID, $studentID]);
$objective = $stmt->fetch();

if (!$objective) {
    echo "Learning Objective not found or you don't have access.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($objective['objectiveName']); ?> - Details</title>
</head>
<body>
    <h2><?php echo htmlspecialchars($objective['objectiveName']); ?></h2>
    <p>Status: <?php echo htmlspecialchars($objective['objectiveStatus']); ?></p>

    <button onclick="window.history.back()">Back to Topic</button> <!-- Back button -->
</body>
</html>
