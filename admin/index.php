
<?php
// Redirect to dashboard if logged in, otherwise to login
require_once '../config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}
?>
