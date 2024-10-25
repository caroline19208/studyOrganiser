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
   <!-- Modal for 'Everyday' -->
   <div id="modal1" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('myModal')">&times;</span>
         <h2>Everyday</h2>
         <p>Assignment details for Everyday</p>
      </div>
   </div>

   <!-- Modal for 'Tuesday and Thursday' -->
   <div id="modal2" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('modal2')">&times;</span>
         <h2>Tuesday and Thursday</h2>
         <p>Assignment details for Tuesday and Thursday</p>
      </div>
   </div>

   <!-- Modal for 'Sunday' -->
   <div id="modal3" class="modal">+
      <div class="modal-content">
         <span class="close" onclick="closeModal('modal2')">&times;</span>
         <h2>Sunday</h2>
         <p>Assignment details for Sunday</p>
      </div>
   </div>

   <!-- Modal for 'Last day of the month' -->
   <div id="modal4" class="modal">
      <div class="modal-content">
         <span class="close" onclick="closeModal('modal4')">&times;</span>
         <h2>Last day of the month</h2>
         <p>Assignment details for last day of the month</p>
      </div>
   </div>

    <script src="script.js"></script>


</body>
</html>