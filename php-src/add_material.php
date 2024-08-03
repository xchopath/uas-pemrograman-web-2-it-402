<?php
require 'conf/db.php';
require 'conf/session_global.php';

// Create dbconnection
$dbconn = new mysqli($servername, $username, $password, $dbname, $dbport);

if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $youtube_url = $_POST['youtube_url'];
    
    // Handle image upload
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $image_path = $target_file;
        
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $message_error = "Sorry, there was an error uploading your file.";
        }
    }

    // Prepare SQL statement
    $stmt = $dbconn->prepare("INSERT INTO materials (title, description, image_path, youtube_url, approval) VALUES (?, ?, ?, ?, ?)");
    $approval = 0; // Default approval status is 0 (not approved)
    $stmt->bind_param("ssssi", $title, $description, $image_path, $youtube_url, $approval);

    if ($stmt->execute()) {
        $message_success = "Materi berhasil ditambahkan dan menunggu persetujuan admin.";
    } else {
        $message_error = "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $dbconn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Materi</title>
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
        <h2 class="text-center mb-4">Tambah Materi</h2>
        <form method="POST" enctype="multipart/form-data">
            <?php if (isset($message_error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($message_error); ?>
                </div>
            <?php elseif (isset($message_success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($message_success); ?>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="title" class="form-label">Judul:</label>
                <input type="text" class="form-control" name="title" id="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi:</label>
                <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Gambar:</label>
                <input type="file" class="form-control" name="image" id="image" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="youtube_url" class="form-label">Embed URL YouTube:</label>
                <input type="url" class="form-control" name="youtube_url" id="youtube_url">
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
