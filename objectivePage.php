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
    // Check if an image file was uploaded without errors
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get image data and add it to the database
        $imageData = file_get_contents($_FILES['image']['tmp_name']); // Read image file as binary data

        // Update the LEARNING_OBJECTIVE table to include the image
        $stmt = $conn->prepare("UPDATE LEARNING_OBJECTIVE SET image = ? WHERE objectiveID = ? AND studentID = ?");
        $stmt->execute([$imageData, $objectiveID, $studentID]);

        // Refresh the page to display the uploaded image
        header("Location: objectivePage.php?objectiveID=$objectiveID");
        exit();
    } else {
        echo "<p>Error uploading image. Please try again.</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($objective['objectiveName']); ?> - Details</title>
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

    <?php if (!empty($objective['image'])): ?>
    <h3>Uploaded Image:</h3>
    <img src="data:image/jpeg;base64,<?php echo base64_encode($objective['image']); ?>" alt="Uploaded Image" style="max-width: 100%; height: auto;"> <!-- Display image from database -->
    <?php endif; ?>

    <!-- Add Image Section -->
<h3>Add Image:</h3>
<button onclick="document.getElementById('imageUploadForm').style.display='block'">Add Image</button>

<!-- Image Upload Form (initially hidden) -->
<div id="imageUploadForm" style="display:none;">
    <form method="POST" enctype="multipart/form-data">
        <label for="image">Select an image:</label>
        <input type="file" name="image" accept="image/*" required> <!-- File input for image -->
        <button type="submit" name="uploadImage">Upload Image</button> <!-- Upload button -->
        <button type="button" onclick="toggleImageForm()">Close</button> <!-- Close button -->
    </form>
</div>

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
