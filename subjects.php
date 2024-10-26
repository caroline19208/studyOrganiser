<?php
session_start();
include_once("connection.php");

// Check if user is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: publicPage.php");
    exit();
}

$studentID = $_SESSION['studentID'];

// Handle form submission to add a new subject
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectName = htmlspecialchars($_POST['subjectName']);

    // Insert subject into the database
    $stmt = $conn->prepare("INSERT INTO SUBJECT (studentID, subjectName) VALUES (?, ?)");
    $stmt->execute([$studentID, $subjectName]);
    $message = "Subject added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title>
</head>
<body>
<h2>Add a New Subject</h2>

<?php if (isset($message)) echo "<p>$message</p>"; ?>

<form method="POST">
    <label for="subjectName">Subject Name:</label>
    <input type="text" name="subjectName" required>
    <button type="submit">Add Subject</button>
</form>

<!-- Display list of subjects -->
<h3>Your Subjects</h3>
<ul>
<?php
// Fetch subjects for the logged-in user
$stmt = $conn->prepare("SELECT * FROM SUBJECT WHERE studentID = ?");
$stmt->execute([$studentID]);
$subjects = $stmt->fetchAll();

foreach ($subjects as $subject) {
    echo "<li>" . htmlspecialchars($subject['subjectName']) . "</li>";
}
?>
</ul>
</body>
</html>
