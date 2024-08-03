<?php
// Database connection settings
require 'conf/db.php';
require 'conf/session_global.php';

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
$discussion_sql = "SELECT * FROM discussions WHERE material_id = ? AND approval = 1 ORDER BY created_at DESC";
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
    <title>View Material - EduWeb</title>
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

            <!-- Discussion Form -->
            <h4 class="mt-5">Mari Berdiskusi</h4>
            <form action="submit_comment.php" method="POST">
                <input type="hidden" name="material_id" value="<?php echo htmlspecialchars($material['id']); ?>">
                <div class="mb-3">
                    <label for="comment" class="form-label">Tambahkan Komentar:</label>
                    <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            <!-- Display Discussions -->
            <h4 class="mt-5">Diskusi</h4>
            <?php if ($discussions->num_rows > 0): ?>
                <?php while ($discussion = $discussions->fetch_assoc()): ?>
                    <div class="border p-3 mb-3 bg-secondary">
                        <div><span class="fw-bolder"><?php echo htmlspecialchars($discussion['username']); ?></span> <span class="badge badge-pill badge-primary bg-primary"><?php echo htmlspecialchars($discussion['created_at']); ?></span></div>
                        <p><?php echo nl2br(htmlspecialchars($discussion['comment'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No comments yet.</p>
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
$discussion_stmt->close();
$dbconn->close();
?>
