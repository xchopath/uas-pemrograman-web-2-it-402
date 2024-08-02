<?php
// Database connection settings
require 'conf/db.php';
require 'conf/session_global.php';

// Create db connection
$dbconn = new mysqli($servername, $username, $password, $dbname, $dbport);

if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $material_id = intval($_POST['material_id']);
    $username = $_SESSION['user'];
    $comment = $_POST['comment'];

    $stmt = $dbconn->prepare("INSERT INTO discussions (material_id, username, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $material_id, $username, $comment);

    if ($stmt->execute()) {
        header("Location: view_material.php?id=" . $material_id);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$dbconn->close();
?>
