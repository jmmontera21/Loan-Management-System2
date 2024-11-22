<?php
session_start();
// Update the last activity timestamp
$_SESSION['last_activity'] = time();
?>
