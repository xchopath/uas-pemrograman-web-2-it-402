<?php
// Database connection settings
require 'conf/db.php';
require 'conf/session_global.php';

// Create db connection
$dbconn = new mysqli($servername, $username, $password, $dbname, $dbport);

if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

// Fetch approved materials, sorted by created_at in descending order
$sql = "SELECT * FROM materials WHERE approval = 1 ORDER BY created_at DESC";
$result = $dbconn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EduWeb</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional Bootstrap Dark Theme -->
    <link href="https://bootswatch.com/5/darkly/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="#">EduWeb</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/add_material.php">Tambah Materi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Materi Edukasi</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card bg-secondary text-light h-100">
                        <?php if (!empty($row['image_path'])): ?>
                            <div class="card-img-top-container" style="height: 200px;">
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top h-100 w-100" alt="Image" style="object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($row['description']); ?></p>
                            <?php if (!empty($row['youtube_url'])): ?>
                                <a href="view_material.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-primary mt-auto">View</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No materials found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$dbconn->close();
?>
