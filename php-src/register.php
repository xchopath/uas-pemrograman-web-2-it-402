<?php
require 'conf/db.php';

// Create dbconnection
$dbconn = new mysqli($servername, $username, $password, $dbname, $dbport);

if ($dbconn->connect_error) {
    die("dbconnection failed: " . $dbconn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $default_role = 'USER';
    $dbstatement = $dbconn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $dbstatement->bind_param("ssss", $username, $email, $password, $default_role);

    if ($dbstatement->execute()) {
        $message_success = 'Registration Success';
    } else {
        $message_error = "Error: " . $dbstatement->error;
    }

    // Close statement
    $dbstatement->close();
}

$dbconn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduWEB - Registration</title>
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
                            <a class="nav-link" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register.php">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
    </nav>
    <div class="container mt-5">
        <div class="mt-5"></div>
        <h2 class="my-4 text-center">Registration</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="register.php" method="POST" class="bg-secondary p-4 rounded">
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
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>


