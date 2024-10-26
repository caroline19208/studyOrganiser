<?php
// Start the PHP session to keep track of user information across pages
session_start();

// Include the database connection file
include_once("connection.php");

// Include the navigation bar for consistent UI across pages
include_once("navbar.php");

// Check if the user is logged in by seeing if their studentID is stored in the session
// If studentID is not set, redirect them to the public page (login page)
if (!isset($_SESSION['studentID'])) {
    header("Location: publicPage.php"); 
    exit(); // Stop further execution to ensure the redirect happens
}

// Retrieve the logged-in user's studentID from the session
$studentID = $_SESSION['studentID'];

// Initialize empty arrays to hold assignments for each status
// These arrays will later be filled with assignments from the database
$confused = [];
$developing = [];
$proficient = [];
$confident = [];

// Prepare SQL queries to fetch assignments for this student based on their status
// Each query is for a specific review status: Confused, Developing, Proficient, Confident
$confusedAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Confused'");
$developingAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Developing'");
$proficientAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Proficient'");
$confidentAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Confident'");

// Execute each query and pass in the student's ID as a parameter
// This fetches assignments for this specific student only
$confusedAssignments->execute([$studentID]);
$developingAssignments->execute([$studentID]);
$proficientAssignments->execute([$studentID]);
$confidentAssignments->execute([$studentID]);

// Retrieve all results from each query and store them in the respective arrays
// Each array will contain all assignments with a particular status for this student
$confused = $confusedAssignments->fetchAll();
$developing = $developingAssignments->fetchAll();
$proficient = $proficientAssignments->fetchAll();
$confident = $confidentAssignments->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Schedule page</title>
   <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS for styling -->
</head>
<body>
<script src="date.js""></script> <!-- Link to external JavaScript file for date handling -->

<!-- Button to trigger popups for different assignment categories -->
<button onclick="openModal('modal1')">Everyday</button>
<button onclick="openModal('modal2')">Tuesday and Thursday</button>
<button onclick="openModal('modal3')">Sunday</button>
<button onclick="openModal('modal4')">Last day of the month</button>

<!-- Modal for 'Everyday' (Confused Status) -->
<div id="modal1" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal1')">&times;</span> <!-- Button to close the modal -->
      <h2>Everyday</h2>
      <!-- Check if the $confused array has assignments -->
      <?php if (!empty($confused)): ?>
         <!-- Loop through each assignment in the $confused array and display details -->
         <?php foreach ($confused as $assignment): ?>
             <div>
                 <h3><?php echo $assignment['assignmentName']; ?></h3>
                 <p>Due: <?php echo $assignment['dueDate']; ?></p>
                 <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                 <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <!-- Message to display if there are no assignments for this status -->
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<!-- Modal for 'Tuesday and Thursday' (Developing Status) -->
<div id="modal2" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal2')">&times;</span>
      <h2>Tuesday and Thursday</h2>
      <?php if (!empty($developing)): ?>
         <?php foreach ($developing as $assignment): ?>
             <div>
                 <h3><?php echo $assignment['assignmentName']; ?></h3>
                 <p>Due: <?php echo $assignment['dueDate']; ?></p>
                 <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                 <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<!-- Modal for 'Sunday' (Proficient Status) -->
<div id="modal3" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal3')">&times;</span>
      <h2>Sunday</h2>
      <?php if (!empty($proficient)): ?>
         <?php foreach ($proficient as $assignment): ?>
             <div>
                 <h3><?php echo $assignment['assignmentName']; ?></h3>
                 <p>Due: <?php echo $assignment['dueDate']; ?></p>
                 <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                 <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<!-- Modal for 'Last day of the month' (Confident Status) -->
<div id="modal4" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal4')">&times;</span>
      <h2>Last day of the month</h2>
      <?php if (!empty($confident)): ?>
         <?php foreach ($confident as $assignment): ?>
             <div>
                 <h3><?php echo $assignment['assignmentName']; ?></h3>
                 <p>Due: <?php echo $assignment['dueDate']; ?></p>
                 <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                 <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<script src="script.js"></script> <!-- Link to external JavaScript for modal functionality -->

</body>
</html>
_