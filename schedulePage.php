<?php
session_start();
include_once("connection.php");
include_once("navbar.php");

// Check if studentID is set
if (!isset($_SESSION['studentID'])) {
    header("Location: publicPage.php"); 
    exit();
}

$studentID = $_SESSION['studentID'];

// Initialize variables as empty arrays
$confused = [];
$developing = [];
$proficient = [];
$confident = [];

// Prepare SQL queries to fetch assignments based on status
$confusedAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Confused'");
$developingAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Developing'");
$proficientAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Proficient'");
$confidentAssignments = $conn->prepare("SELECT * FROM ASSIGNMENT WHERE studentID = ? AND reviewStatus = 'Confident'");

// Execute queries and fetch results if there are assignments
$confusedAssignments->execute([$studentID]);
$developingAssignments->execute([$studentID]);
$proficientAssignments->execute([$studentID]);
$confidentAssignments->execute([$studentID]);

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
   <link rel="stylesheet" href="styles.css">
</head>
<body>
<script src="date.js""></script>
<!-- Button to trigger popup -->
   <button onclick="openModal('modal1')">Everyday</button>
   <button onclick="openModal('modal2')">Tuesday and Thursday</button>
   <button onclick="openModal('modal3')">Sunday</button>
   <button onclick="openModal('modal4')">Last day of the month</button>
<!-- The Modal which can't be seen until button is clicked -->
   <!-- Modal for 'Everyday' (CONFUSED STATUS) -->
   <div id="modal1" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('myModal')">&times;</span>
         <h2>Everyday</h2>
         <?php foreach ($confused as $assignment): ?>
            <div>
                <h3><?php echo $assignment['assignmentName']; ?></h3>
                <p>Due: <?php echo $assignment['dueDate']; ?></p>
                <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
            </div>
        <?php endforeach; ?>
      </div>
   </div>

   <!-- Modal for 'Tuesday and Thursday' (CONFUSED STATUS) -->
   <div id="modal2" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('modal2')">&times;</span>
         <h2>Tuesday and Thursday</h2>
         <?php foreach ($developing as $assignment): ?>
            <div>
                <h3><?php echo $assignment['assignmentName']; ?></h3>
                <p>Due: <?php echo $assignment['dueDate']; ?></p>
                <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
            </div>
        <?php endforeach; ?>
      </div>
   </div>

   <!-- Modal for 'Sunday' -->
   <div id="modal3" class="modal">+
      <div class="modal-content">
         <span class="close" onclick="closeModal('modal2')">&times;</span>
         <h2>Sunday</h2>
         <?php foreach ($proficient as $assignment): ?>
            <div>
                <h3><?php echo $assignment['assignmentName']; ?></h3>
                <p>Due: <?php echo $assignment['dueDate']; ?></p>
                <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
            </div>
        <?php endforeach; ?>
      </div>
   </div>

   <!-- Modal for 'Last day of the month' -->
   <div id="modal4" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('modal4')">&times;</span>
         <h2>Last day of the month</h2>
         <?php foreach ($confident as $assignment): ?>
            <div>
                <h3><?php echo $assignment['assignmentName']; ?></h3>
                <p>Due: <?php echo $assignment['dueDate']; ?></p>
                <p>Coins Earned: <?php echo $assignment['coinsEarned']; ?></p>
                <p>Status: <?php echo $assignment['reviewStatus']; ?></p>
            </div>
        <?php endforeach; ?>
      </div>
   </div>

    <script src="script.js"></script>


</body>
</html>