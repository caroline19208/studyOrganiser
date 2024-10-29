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
    <title><?php echo htmlspecialchars($topic['topicName']); ?> - Learning Objectives</title>
</head>
<body>

<button onclick="window.history.back()">Back to Topics List</button> <!-- Back button -->

<h2><?php echo htmlspecialchars($topic['topicName']); ?> - Learning Objectives</h2>
<?php if (isset($message)) echo "<p>$message</p>"; ?>

<!-- Form to add a new learning objective -->
<form method="POST">
    <label for="objectiveName">Learning Objective:</label>
    <input type="text" name="objectiveName" required> <!-- Text input for objective name -->
    
    <label for="objectiveStatus">Current Status:</label>
    <select name="objectiveStatus" required> <!-- Dropdown for status -->
        <option value="Not started">Not started</option>
        <option value="Confused">Confused</option>
        <option value="Developing">Developing</option>
        <option value="Proficient">Proficient</option>
        <option value="Confident">Confident</option>
        <option value="Retired">Retired</option>
    </select>

    <button type="submit" name="addObjective">Add Learning Objective</button> <!-- Submit button -->
</form>

<?php
// Handle form submission to add a learning objective
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addObjective'])) {
    $objectiveName = htmlspecialchars($_POST['objectiveName']); // Sanitize input
    $objectiveStatus = htmlspecialchars($_POST['objectiveStatus']); // Capture selected status

    // Insert new learning objective into the database
    $stmt = $conn->prepare("INSERT INTO LEARNING_OBJECTIVE (topicID, studentID, objectiveName, objectiveStatus) VALUES (?, ?, ?, ?)");
    $stmt->execute([$topicID, $studentID, $objectiveName, $objectiveStatus]);

    $message = "Learning objective added successfully!";
}
?>

<?php
// Fetch all learning objectives for this topic
$stmt = $conn->prepare("SELECT * FROM LEARNING_OBJECTIVE WHERE topicID = ? AND studentID = ?");
$stmt->execute([$topicID, $studentID]);
$objectives = $stmt->fetchAll();
?>

<h3>Learning Objectives</h3>
<ul>
    <?php foreach ($objectives as $objective): ?>
        <li>
            <a href="objectivePage.php?objectiveID=<?php echo $objective['objectiveID']; ?>">
                <?php echo htmlspecialchars($objective['objectiveName']); ?>
            </a> - <?php echo htmlspecialchars($objective['objectiveStatus']); ?>
        </li>
    <?php endforeach; ?>
</ul>





</body>
</html>