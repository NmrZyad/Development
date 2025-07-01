<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Delete cookie
setcookie("remember_me", "", time() - 3600, "/");

// Redirect to login , the session used here again for viewing the massage only one time 
session_start();
$_SESSION['flash'] = 'You\'ve successfully logged out.';
header("Location: login.php");
exit();
