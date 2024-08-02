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

// Determine the status filter
$status = isset($_GET['status']) ? intval($_GET['status']) : -1;

// Prepare SQL query based on the status filter
if ($status == -1) {
    $sql = "SELECT * FROM materials ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM materials WHERE approval_status = ? ORDER BY created_at DESC";
}

$stmt = $dbconn->prepare($sql);
if ($status != -1) {
    $stmt->bind_param("i", $status);
}
$stmt->execute();
$result = $stmt->get_result();

$dbconn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
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
        <h2 class="text-center mb-4">Materials</h2>
        
        <!-- Filter Buttons -->
        <div class="mb-4 text-center">
            <a href="/admin/dashboard.php?status=-1" class="btn btn-primary">All</a>
            <a href="/admin/dashboard.php?status=0" class="btn btn-warning">Pending Approval</a>
            <a href="/admin/dashboard.php?status=1" class="btn btn-success">Approved</a>
            <a href="/admin/dashboard.php?status=2" class="btn btn-danger">Rejected</a>
        </div>

        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Title</th>
                    <th scope="col" class="col-2">Status</th>
                    <th scope="col" class="col-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><a href="/admin/view_material.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="link-offset-2 link-underline link-underline-opacity-0"><?php echo htmlspecialchars($row['title']); ?></a></td>
                            <td>
                                <?php
                                switch ($row['approval']) {
                                    case 0:
                                        echo '<span class="badge bg-warning">Pending Approval</span>';
                                        break;
                                    case 1:
                                        echo '<span class="badge bg-success">Approved</span>';
                                        break;
                                    case 2:
                                        echo '<span class="badge bg-danger">Rejected</span>';
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($row['approval'] == 0): ?>
                                    <a href="approve_material.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                    <a href="reject_material.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No materials found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
