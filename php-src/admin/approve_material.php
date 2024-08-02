<?php
// Start session
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin'])) {
    header("Location: /admin/login.php");
    exit();
}

// Database connection settings
require '../conf/db.php';

// Create db connection
$dbconn = new mysqli($servername, $username, $password, $dbname, $dbport);

if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

if (isset($_GET['id'])) {
    $material_id = intval($_GET['id']);

    $stmt = $dbconn->prepare("UPDATE materials SET approval = 1 WHERE id = ?");
    $stmt->bind_param("i", $material_id);

    if ($stmt->execute()) {
        header("Location: /admin/dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$dbconn->close();
?>
