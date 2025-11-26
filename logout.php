<?php
session_start();

include 'conn.php';
// Destroy the session to log out the user
session_destroy();
// Redirect to the login page
header("Location: index.php");
exit();

?>