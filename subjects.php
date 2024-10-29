<?php
//to access session variables.
session_start();
include_once("connection.php");
include("navbar.php");
//checks if studentID set in session
if (!isset($_SESSION['studentID'])) {
    //redirect to public page if studentID not set
    header("Location: publicPage.php"); 
    exit();
}

//stores logged-in user's ID for database queries.
$studentID = $_SESSION['studentID'];

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectName = htmlspecialchars($_POST['subjectName']); // Get and sanitize the subject name

    // Prepare an SQL query to insert the new subject into the SUBJECT table
    $stmt = $conn->prepare("INSERT INTO SUBJECT (studentID, subjectName) VALUES (?, ?)");
    $stmt->execute([$studentID, $subjectName]); // Execute the query with the student's ID and subject name
    
    $message = "Subject added successfully!"; // Set a message to confirm addition
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title> <!-- Page title -->
</head>
<body>
<h2>Add a New Subject</h2> <!-- Page heading -->

<?php if (isset($message)) echo "<p>$message</p>"; ?> <!-- Display success message if available -->

<!-- Form for adding a new subject -->
<form method="POST">
    <label for="subjectName">Subject Name:</label>
    <input type="text" name="subjectName" required> <!-- Text input for subject name -->
    <button type="submit">Add Subject</button> <!-- Submit button -->
</form>

<!-- Display list of subjects -->
<h3>Your Subjects</h3>
<ul>
<?php
// Prepare and execute a query to fetch the subjects for the logged-in student
$stmt = $conn->prepare("SELECT * FROM SUBJECT WHERE studentID = ?");
$stmt->execute([$studentID]);
if (!$stmt->execute()) {
    echo "Error creating SUBJECT table: " . $conn->errorInfo()[2];
}
$subjects = $stmt->fetchAll(); // Fetch all subjects for this student

// Loop through each subject and display it as a list item
foreach ($subjects as $subject) {
    $subjectID = $subject['subjectID'];
    echo "<li><a href='subjectPage.php?subjectID=$subjectID'>" . htmlspecialchars($subject['subjectName']) . "</a></li>";
}

?>
</ul>
</body>
</html>
