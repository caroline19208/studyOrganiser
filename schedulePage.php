<?php
session_start();
include_once("connection.php");
include_once("navbar.php");

if (!isset($_SESSION['studentID'])) {
    header("Location: publicPage.php");
    exit();
}

$studentID = $_SESSION['studentID'];

// Fetch assignments based on review status with learning objective name
$confusedAssignments = $conn->prepare("SELECT ASSIGNMENT.*, LEARNING_OBJECTIVE.objectiveName 
                                       FROM ASSIGNMENT 
                                       JOIN LEARNING_OBJECTIVE ON ASSIGNMENT.objectiveID = LEARNING_OBJECTIVE.objectiveID 
                                       WHERE ASSIGNMENT.studentID = ? AND ASSIGNMENT.reviewStatus = 'Confused'");
$developingAssignments = $conn->prepare("SELECT ASSIGNMENT.*, LEARNING_OBJECTIVE.objectiveName 
                                         FROM ASSIGNMENT 
                                         JOIN LEARNING_OBJECTIVE ON ASSIGNMENT.objectiveID = LEARNING_OBJECTIVE.objectiveID 
                                         WHERE ASSIGNMENT.studentID = ? AND ASSIGNMENT.reviewStatus = 'Developing'");
$proficientAssignments = $conn->prepare("SELECT ASSIGNMENT.*, LEARNING_OBJECTIVE.objectiveName 
                                         FROM ASSIGNMENT 
                                         JOIN LEARNING_OBJECTIVE ON ASSIGNMENT.objectiveID = LEARNING_OBJECTIVE.objectiveID 
                                         WHERE ASSIGNMENT.studentID = ? AND ASSIGNMENT.reviewStatus = 'Proficient'");
$confidentAssignments = $conn->prepare("SELECT ASSIGNMENT.*, LEARNING_OBJECTIVE.objectiveName 
                                        FROM ASSIGNMENT 
                                        JOIN LEARNING_OBJECTIVE ON ASSIGNMENT.objectiveID = LEARNING_OBJECTIVE.objectiveID 
                                        WHERE ASSIGNMENT.studentID = ? AND ASSIGNMENT.reviewStatus = 'Confident'");

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
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Schedule Page</title>
   <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Your Schedule</h1>
<script src="date.js"></script>

<!-- Buttons to open each modal with updated labels -->
<button onclick="openModal('modal1')">Everyday</button>
<button onclick="openModal('modal2')">Tuesday and Thursday</button>
<button onclick="openModal('modal3')">Sunday</button>
<button onclick="openModal('modal4')">End of the Month</button>

<!-- Modal for Everyday assignments -->
<div id="modal1" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal1')">&times;</span>
      <h2>Everyday</h2>
      <?php if (!empty($confused)): ?>
         <?php foreach ($confused as $assignment): ?>
             <div class="assignment-box">
                 <h3><?php echo htmlspecialchars($assignment['objectiveName']); ?></h3>
                 <p><?php echo htmlspecialchars($assignment['details']); ?></p>
                 <p>Value: <?php echo htmlspecialchars($assignment['coinsEarned']); ?></p>
                 <form method="POST" action="objectivePage.php?objectiveID=<?php echo $assignment['objectiveID']; ?>">
                    <input type="hidden" name="assignmentID" value="<?php echo $assignment['assignmentID']; ?>">
                    <button type="submit" name="deleteAssignment" class="delete-btn">Delete</button>
                 </form>
                 <button class="complete-btn">Complete</button>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<!-- Modal for Tuesday and Thursday assignments -->
<div id="modal2" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal2')">&times;</span>
      <h2>Tuesday and Thursday</h2>
      <?php if (!empty($developing)): ?>
         <?php foreach ($developing as $assignment): ?>
             <div class="assignment-box">
                 <h3><?php echo htmlspecialchars($assignment['objectiveName']); ?></h3>
                 <p><?php echo htmlspecialchars($assignment['details']); ?></p>
                 <p>Value: <?php echo htmlspecialchars($assignment['coinsEarned']); ?></p>
                 <form method="POST" action="objectivePage.php?objectiveID=<?php echo $assignment['objectiveID']; ?>">
                    <input type="hidden" name="assignmentID" value="<?php echo $assignment['assignmentID']; ?>">
                    <button type="submit" name="deleteAssignment" class="delete-btn">Delete</button>
                 </form>
                 <button class="complete-btn">Complete</button>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<!-- Modal for Sunday assignments -->
<div id="modal3" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal3')">&times;</span>
      <h2>Sunday</h2>
      <?php if (!empty($proficient)): ?>
         <?php foreach ($proficient as $assignment): ?>
             <div class="assignment-box">
                 <h3><?php echo htmlspecialchars($assignment['objectiveName']); ?></h3>
                 <p><?php echo htmlspecialchars($assignment['details']); ?></p>
                 <p>Value: <?php echo htmlspecialchars($assignment['coinsEarned']); ?></p>
                 <form method="POST" action="objectivePage.php?objectiveID=<?php echo $assignment['objectiveID']; ?>">
                    <input type="hidden" name="assignmentID" value="<?php echo $assignment['assignmentID']; ?>">
                    <button type="submit" name="deleteAssignment" class="delete-btn">Delete</button>
                 </form>
                 <button class="complete-btn">Complete</button>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<!-- Modal for End of the Month assignments -->
<div id="modal4" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal('modal4')">&times;</span>
      <h2>End of the Month</h2>
      <?php if (!empty($confident)): ?>
         <?php foreach ($confident as $assignment): ?>
             <div class="assignment-box">
                 <h3><?php echo htmlspecialchars($assignment['objectiveName']); ?></h3>
                 <p><?php echo htmlspecialchars($assignment['details']); ?></p>
                 <p>Value: <?php echo htmlspecialchars($assignment['coinsEarned']); ?></p>
                 <form method="POST" action="objectivePage.php?objectiveID=<?php echo $assignment['objectiveID']; ?>">
                    <input type="hidden" name="assignmentID" value="<?php echo $assignment['assignmentID']; ?>">
                    <button type="submit" name="deleteAssignment" class="delete-btn">Delete</button>
                 </form>
                 <button class="complete-btn">Complete</button>
             </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p>No assignments scheduled.</p>
      <?php endif; ?>
   </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    window.onclick = function(event) {
        const modals = document.getElementsByClassName('modal');
        for (let modal of modals) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }
</script>

<style>
    .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
    .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; }
    .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }

    .assignment-box {
        background-color: #e0f0ff;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .assignment-box h3 {
        font-weight: bold;
        color: #333;
    }

    .complete-btn, .delete-btn {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 5px;
    }

    .delete-btn {
        background-color: #f44336;
    }
</style>

</body>
</html>
