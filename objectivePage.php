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

    // Update the notes for this learning objective
    $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET notes = ? WHERE objectiveID = ? AND studentID = ?");
    $stmt->execute([$notes, $objectiveID, $studentID]);

    // Refresh the page to display updated notes
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uploadImage'])) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']); // Get image data

        // Update the image for this learning objective
        $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET image = ? WHERE objectiveID = ? AND studentID = ?");
        $stmt->execute([$imageData, $objectiveID, $studentID]);

        // Refresh the page to display the updated image
        header("Location: objectivePage.php?objectiveID=$objectiveID");
        exit();
    }
}

// Handle image deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteImage'])) {
    // Remove the image from the learning objective
    $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET image = NULL WHERE objectiveID = ? AND studentID = ?");
    $stmt->execute([$objectiveID, $studentID]);

    // Refresh the page to remove the displayed image
    header("Location: objectivePage.php?objectiveID=$objectiveID");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($objective['objectiveName']); ?> - Details</title>
    <style>
    img {
        max-width: 100%;
        height: auto; /* Maintain aspect ratio */
        display: block; /* Center image alignment */
        overflow: auto;
    }
    </style>
</head>
<body>
    <button onclick="window.history.back()">Back to Topic</button> <!-- Back button -->
    <h2><?php echo htmlspecialchars($objective['objectiveName']); ?></h2>
    <p>Status: <?php echo htmlspecialchars($objective['objectiveStatus']); ?></p> 

    <!-- Notes Section -->
    <h3>Notes:</h3>
    <form method="POST">
        <textarea name="notes" rows="10" cols="50"><?php echo htmlspecialchars($objective['notes']); ?></textarea> <!-- Text area to input notes -->
        <button type="submit" name="saveNotes">Save Notes</button> <!-- Submit button -->
    </form>

    <!-- Display Uploaded Image -->
    <?php if (!empty($objective['image'])): ?>
        <h3>Uploaded Image:</h3>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($objective['image']); ?>" alt="Uploaded Image">
        
        <!-- Delete Image Button -->
        <form method="POST" style="display:inline;">
            <button type="submit" name="deleteImage">Delete Image</button>
        </form>
    <?php else: ?>
        <p>No image uploaded.</p>
    <?php endif; ?>

    <!-- Add Image Section -->
    <h3>Add Image:</h3>
    <button onclick="toggleImageForm()">Add Image</button> <!-- Button to toggle the form -->

    <!-- Image Upload Form (initially hidden) -->
    <div id="imageUploadForm" style="display:none;">
        <form method="POST" enctype="multipart/form-data">
            <label for="image">Select an image:</label>
            <input type="file" name="image" accept="image/*" required> <!-- File input for image -->
            <button type="submit" name="uploadImage">Upload Image</button> <!-- Upload button -->
            <button type="button" onclick="toggleImageForm()">Close</button> <!-- Close button -->
        </form>
    </div>

    <h3>Add Relevant Link:</h3>
<form method="POST">
    <label for="linkURL">URL:</label>
    <input type="url" name="linkURL" required> <!-- Link URL input -->
    
    <label for="linkDescription">Description:</label>
    <input type="text" name="linkDescription" placeholder="Optional description"> <!-- Optional description -->

    <button type="submit" name="addLink">Add Link</button> <!-- Submit button -->
</form>


    <!-- JavaScript to Toggle the Form Visibility -->
    <script>
        function toggleImageForm() {
            var form = document.getElementById("imageUploadForm");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block"; // Show form
            } else {
                form.style.display = "none"; // Hide form
            }
        }
    </script>
</body>
</html>
