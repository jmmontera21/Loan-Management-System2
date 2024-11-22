<?php
$conn = mysqli_connect('localhost', 'root', '', 'lms');
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>
