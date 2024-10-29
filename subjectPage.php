<?php
session_start();
include_once("connection.php");
include("navbar.php");

// Check if user is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: publicPage.php");
    exit();
}

$studentID = $_SESSION['studentID'];

// Get subjectID from the URL
$subjectID = $_GET['subjectID'] ?? null;

// Query the database to verify if the subject belongs to the logged-in student
$stmt = $conn->prepare("SELECT * FROM SUBJECT WHERE subjectID = ? AND studentID = ?");
$stmt->execute([$subjectID, $studentID]);
$subject = $stmt->fetch();

if (!$subject) {
    echo "Subject not found or access denied.";
    exit();
}

// Handle form submission to add a topic
if (isset($_POST['addTopic'])) {
    $topicName = htmlspecialchars($_POST['topicName']); // Sanitize topic name

    // Insert the new topic into the TOPIC table
    $stmt = $conn->prepare("INSERT INTO TOPIC (subjectID, studentID, topicName) VALUES (?, ?, ?)");
    $stmt->execute([$subjectID, $studentID, $topicName]);

    $message = "Topic added successfully!"; // Display confirmation message
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($subject['subjectName']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($subject['subjectName']); ?></h1>
    <p>This is the dedicated page for the subject: <?php echo htmlspecialchars($subject['subjectName']); ?></p>

    <h3>Add a New Topic</h3>
    <form method="POST" action="">
        <label for="topicName">Topic Name:</label>
        <input type="text" name="topicName" required>
        <button type="submit" name="addTopic">Add Topic</button>
    </form>

</body>
</html>

<?php if (isset($message)) echo "<p>$message</p>"; ?> <!-- Display success message if available -->

<h3>Your Topics</h3>
<ul>
<?php
// Fetch topics for this subject
$stmt = $conn->prepare("SELECT * FROM TOPIC WHERE subjectID = ? AND studentID = ?");
$stmt->execute([$subjectID, $studentID]);
$topics = $stmt->fetchAll();

// Display each topic
foreach ($topics as $topic) {
    echo "<li><a href='topicPage.php?topicID=" . $topic['topicID'] . "'>" . htmlspecialchars($topic['topicName']) . "</a></li>";
}
?>
</ul>


