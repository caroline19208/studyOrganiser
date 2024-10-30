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

// Handle form submission for saving notes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveNotes'])) {
    $notes = htmlspecialchars($_POST['notes']); // Sanitize input
    $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET notes = ? WHERE objectiveID = ? AND studentID = ?");
    $stmt->execute([$notes, $objectiveID, $studentID]);
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}

// Handle image upload and deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uploadImage'])) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET image = ? WHERE objectiveID = ? AND studentID = ?");
        $stmt->execute([$imageData, $objectiveID, $studentID]);
        header("Location: objectivePage.php?objectiveID=$objectiveID");
        exit();
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteImage'])) {
    $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET image = NULL WHERE objectiveID = ? AND studentID = ?");
    $stmt->execute([$objectiveID, $studentID]);
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}

// Handle adding and deleting links
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addLink'])) {
    $linkURL = $_POST['linkURL'];
    $linkDescription = $_POST['linkDescription'] ?? '';
    $stmt = $conn->prepare("INSERT INTO LINKS (objectiveID, linkURL, linkDescription) VALUES (?, ?, ?)");
    $stmt->execute([$objectiveID, $linkURL, $linkDescription]);
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteLink'])) {
    $linkID = $_POST['linkID'];
    $stmt = $conn->prepare("DELETE FROM LINKS WHERE linkID = ?");
    $stmt->execute([$linkID]);
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}

// Handle creating an assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createAssignment'])) {
    $status = htmlspecialchars($_POST['objectiveStatus']);
    $assignmentDetails = htmlspecialchars($_POST['assignmentDetails']);
    $assignmentValue = (int)$_POST['assignmentValue'];

    // Update the learning objective status
    $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET objectiveStatus = ? WHERE objectiveID = ? AND studentID = ?");
    $stmt->execute([$status, $objectiveID, $studentID]);

    // Insert the new assignment
    $stmt = $conn->prepare("INSERT INTO ASSIGNMENT (objectiveID, studentID, details, coinsEarned) VALUES (?, ?, ?, ?)");
    $stmt->execute([$objectiveID, $studentID, $assignmentDetails, $assignmentValue]);

    // Display confirmation message
    $assignmentMessage = "Assignment created successfully and added to your schedule!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($objective['objectiveName']); ?> - Details</title>
    <style>
        .centered-form { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); width: 300px; text-align: center; }
        img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <button onclick="window.history.back()">Back to Topic</button>
    <h2><?php echo htmlspecialchars($objective['objectiveName']); ?></h2>
    <p>Status: <?php echo htmlspecialchars($objective['objectiveStatus']); ?></p>

    <!-- Display any assignment message -->
    <?php if (isset($assignmentMessage)): ?>
        <p style="color: green;"><?php echo $assignmentMessage; ?></p>
    <?php endif; ?>

    <!-- Notes Section -->
    <h3>Notes:</h3>
    <form method="POST">
        <textarea name="notes" rows="10" cols="50"><?php echo htmlspecialchars($objective['notes']); ?></textarea>
        <button type="submit" name="saveNotes">Save Notes</button>
    </form>

    <!-- Image Section -->
    <h3>Uploaded Image:</h3>
    <?php if (!empty($objective['image'])): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($objective['image']); ?>" alt="Uploaded Image">
        <form method="POST"><button type="submit" name="deleteImage">Delete Image</button></form>
    <?php else: ?>
        <p>No image uploaded.</p>
    <?php endif; ?>
    <button onclick="toggleForm('imageForm')">Add Image</button>
    <div id="imageForm" class="centered-form">
        <form method="POST" enctype="multipart/form-data">
            <label>Select an image:</label><input type="file" name="image" required><button type="submit" name="uploadImage">Upload</button><button type="button" onclick="toggleForm('imageForm')">Close</button>
        </form>
    </div>

    <!-- Links Section -->
    <h3>Relevant Links:</h3>
    <ul>
        <?php
        $stmt = $conn->prepare("SELECT * FROM LINKS WHERE objectiveID = ?");
        $stmt->execute([$objectiveID]);
        $links = $stmt->fetchAll();
        if ($links) {
            foreach ($links as $link) {
                echo "<li><a href='" . htmlspecialchars($link['linkURL']) . "' target='_blank'>" . htmlspecialchars($link['linkURL']) . "</a> - " . htmlspecialchars($link['linkDescription']) . "<form method='POST' style='display:inline;'><input type='hidden' name='linkID' value='" . $link['linkID'] . "'><button type='submit' name='deleteLink'>Delete</button></form></li>";
            }
        } else {
            echo "<p>No links added yet.</p>";
        }
        ?>
    </ul>
    <button onclick="toggleForm('linkForm')">Add Link</button>
    <div id="linkForm" class="centered-form">
        <form method="POST">
            <label>URL:</label><input type="url" name="linkURL" required><label>Description:</label><input type="text" name="linkDescription"><button type="submit" name="addLink">Add Link</button><button type="button" onclick="toggleForm('linkForm')">Close</button>
        </form>
    </div>

    <!-- Assignment Section -->
    <button onclick="toggleForm('statusForm')">Create Assignment</button>
    <div id="statusForm" class="centered-form">
        <form method="POST">
            <h3>Update Status</h3>
            <select name="objectiveStatus" required>
                <option value="Not started">Not started</option>
                <option value="Confused">Confused</option>
                <option value="Developing">Developing</option>
                <option value="Confident">Confident</option>
                <option value="Exam-ready">Exam-ready</option>
                <option value="Retired">Retired</option>
            </select>
            <button type="button" onclick="toggleForm('assignmentDetailsForm')">Next</button>
        </form>
    </div>

    <div id="assignmentDetailsForm" class="centered-form">
        <form method="POST">
            <h3>Create New Assignment</h3>
            <label>Details:</label><textarea name="assignmentDetails" required></textarea><label>Value:</label><input type="number" name="assignmentValue" min="1" required>
            <button type="submit" name="createAssignment">Submit Assignment</button>
        </form>
    </div>

    <!-- JavaScript to Toggle Forms -->
    <script>
        function toggleForm(formId) {
            var form = document.getElementById(formId);
            form.style.display = form.style.display === "none" ? "block" : "none";
        }
    </script>
</body>
</html>
