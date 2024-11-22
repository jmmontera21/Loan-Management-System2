<?php
// On logout, clear local storage as well
echo "<script>localStorage.clear();</script>";

// Destroy session
session_destroy();

// Redirect to homepage or login page
header('Location: homepage.php');
exit;
?>
