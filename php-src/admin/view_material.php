<?php

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

$material_id = intval($_GET['id']); // Sanitize input

// Fetch material details
$sql = "SELECT * FROM materials WHERE id = ?";
$stmt = $dbconn->prepare($sql);
$stmt->bind_param("i", $material_id);
$stmt->execute();
$material = $stmt->get_result()->fetch_assoc();

// Fetch discussions for the material
$discussion_sql = "SELECT * FROM discussions WHERE material_id = ? ORDER BY created_at DESC";
$discussion_stmt = $dbconn->prepare($discussion_sql);
$discussion_stmt->bind_param("i", $material_id);
$discussion_stmt->execute();
$discussions = $discussion_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduWeb Admin | View Material</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional Bootstrap Dark Theme -->
    <link href="https://bootswatch.com/5/darkly/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="#">EduWeb Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <?php if ($material): ?>
            <h2 class="text-center mb-4"><?php echo htmlspecialchars($material['title']); ?></h2>
            <?php if (!empty($material['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($material['image']); ?>" class="img-fluid mb-4" alt="Image">
            <?php endif; ?>
            <p><?php echo htmlspecialchars($material['description']); ?></p>
            <?php if (!empty($material['youtube_url'])): ?>
                <div class="col-12 col-md-8 offset-md-2 col-lg-8 offset-lg-2">
                <div class="ratio ratio-16x9 text-center mt-4 mb-4 ">
                    <iframe class="embed-responsive-item" src="<?php echo htmlspecialchars($material['youtube_url']); ?>" allowfullscreen></iframe>
                </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p>Material not found.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connections
$stmt->close();
$dbconn->close();
?>
