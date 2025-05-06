
<?php
require_once '../config.php';

// Clear session variables
session_unset();
session_destroy();

// Redirect to login
redirect('login.php');
?>
