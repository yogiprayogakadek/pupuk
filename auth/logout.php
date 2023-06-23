<?php
require_once('../config/config.php');
if (isset($_POST['logout']) && $_POST['logout'] == 1) {
    // Perform logout logic here, such as destroying the session
    session_start();
    session_destroy();
    
    // Redirect the user to the login page or any desired page after logout
    header('Location:' . $baseUrl . '/auth/login.php');
    exit();
}