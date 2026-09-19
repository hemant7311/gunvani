<?php
session_start();

// destroy session
session_unset();
session_destroy();

// redirect to home
header("Location: index.php");
exit();
?>