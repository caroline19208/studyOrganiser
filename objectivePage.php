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

// Check if $objectiveID is provided
if (!$objectiveID) {
    echo "No objective ID provided.";
    exit();
}

// Fetch learning objective details
$stmt = $conn->prepare("SELECT * FROM LEARNING_OBJECTIVE WHERE objectiveID = ? AND studentID = ?");
$stmt->execute([$objectiveID, $studentID]);
$objective = $stmt->fetch();

// Check if objective was found
if (!$objective) {
    echo "Learning Objective not found or you don't have access.";
    exit();
}

// Safely access reviewStatus with a default fallback
$status = $objective['reviewStatus'] ?? 'Not started';

// Fetch assignments related to this learning objective
$assignmentsStmt = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE objectiveID = ? AND studentID = ?");
$assignmentsStmt->execute([$objectiveID, $studentID]);
$assignments = $assignmentsStmt->fetchAll();

// Handle assignment deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteAssignment'])) {
    $assignmentID = $_POST['assignmentID'];
    $stmt = $conn->prepare("DELETE FROM ASSIGNMENT WHERE assignmentID = ? AND studentID = ?");
    $stmt->execute([$assignmentID, $studentID]);
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}

// Handle form submission for saving notes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveNotes'])) {
    $notes = htmlspecialchars($_POST['notes']);
    $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET notes = ? WHERE objectiveID = ? AND studentID = ?");
    $stmt->execute([$notes, $objectiveID, $studentID]);
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}

// Handle creating an assignment, ensuring only one active assignment per objective
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createAssignment']) && empty($assignments)) {
    $reviewStatus = htmlspecialchars($_POST['reviewStatus']);
    $assignmentDetails = htmlspecialchars($_POST['assignmentDetails']);
    $assignmentValue = (int)$_POST['assignmentValue'];

    // Update the learning objective status
    $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET reviewStatus = ? WHERE objectiveID = ? AND studentID = ?");
    $stmt->execute([$reviewStatus, $objectiveID, $studentID]);

    // Insert the new assignment
    $stmt = $conn->prepare("INSERT INTO ASSIGNMENT (objectiveID, studentID, details, coinsEarned, reviewStatus) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$objectiveID, $studentID, $assignmentDetails, $assignmentValue, $reviewStatus]);

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
        .assignment-box {
            background-color: #e0f0ff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button onclick="window.history.back()">Back to Topic</button>
    <h2><?php echo htmlspecialchars($objective['objectiveName']); ?></h2>
    <p>Status: <?php echo htmlspecialchars($status); ?></p>

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

    <!-- Assignments Section for this Objective -->
    <h3>Assignments for This Objective:</h3>
    <?php if (!empty($assignments)): ?>
        <?php foreach ($assignments as $assignment): ?>
            <div class="assignment-box">
                <h4><?php echo htmlspecialchars($assignment['details']); ?></h4>
                <p>Value: <?php echo htmlspecialchars($assignment['coinsEarned']); ?></p>
                <p>Status: <?php echo htmlspecialchars($assignment['reviewStatus']); ?></p>
                <form method="POST">
                    <input type="hidden" name="assignmentID" value="<?php echo $assignment['assignmentID']; ?>">
                    <button type="submit" name="deleteAssignment" class="delete-btn">Delete Assignment</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No assignments created for this objective.</p>
    <?php endif; ?>

    <!-- Assignment Creation Form (only if no assignment exists) -->
    <?php if (empty($assignments)): ?>
        <button onclick="toggleForm('statusForm')">Create Assignment</button>
        <div id="statusForm" class="centered-form">
            <form method="POST">
                <h3>Create New Assignment</h3>
                <select name="reviewStatus" required>
                    <option value="Confused">Confused</option>
                    <option value="Developing">Developing</option>
                    <option value="Confident">Confident</option>
                    <option value="Exam-ready">Exam-ready</option>
                </select>
                <label>Details:</label><textarea name="assignmentDetails" required></textarea>
                <label>Value:</label><input type="number" name="assignmentValue" min="1" required>
                <button type="submit" name="createAssignment">Submit Assignment</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- JavaScript to Toggle Forms -->
    <script>
        function toggleForm(formId) {
            var form = document.getElementById(formId);
            form.style.display = form.style.display === "block" ? "none" : "block";
        }
    </script>
</body>
</html>
