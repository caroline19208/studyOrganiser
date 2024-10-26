<!DOCTYPE html>
<html>
<head>
    
    <title>Public page</title>
    
</head>
<body>
<?php
session_Start(); 
// Check if there is a success message to display
if (isset($_SESSION['message'])) {
  echo "<p>" . $_SESSION['message'] . "</p>";
  // Unset the message after displaying it
  unset($_SESSION['message']);
}
?>
<form action="signup.php" method = "POST">
  Username <input type="text" name="username"><br>
  Password <input type="password" name="passwd"><br>
  <br>

  <input type="submit" value="Sign up">
</form>

<form action="login.php" method = "POST">
  Username <input type="text" name="username"><br>
  Password <input type="password" name="passwd"><br>
  <br>

  <input type="submit" value="Login">
</form>

</body>
</html>
