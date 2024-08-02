<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();
    
    // Redirect to the homepage or login page
    header("Location: /index.php");
    exit();
} else {
    // If no session exists, redirect to login page
    header("Location: /login.php");
    exit();
}
?>