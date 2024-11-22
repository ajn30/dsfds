<?php
// Include database connection
include('db.php');
session_start(); // Start session for login

if (isset($_POST['login'])) {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $stmt = $conn->prepare("SELECT UserID, Name, UserType, Password FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userID, $name, $userType, $hashedPassword);
    $stmt->fetch();

    // Check if the user exists and password matches
    if ($userID && password_verify($password, $hashedPassword)) {
        // Set session variables
        $_SESSION['userID'] = $userID;
        $_SESSION['name'] = $name;
        $_SESSION['userType'] = $userType;

        // Redirect to home page
        header("Location: home.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library Management System</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <header class="bg-primary text-white text-center py-4">
        <h1>Login to Library Management System</h1>
    </header>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Login</h5>

                        <!-- Login Form -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <?php if (isset($error)) { ?>
                                <div class="alert alert-danger mt-3">
                                    <?php echo $error; ?>
                                </div>
                            <?php } ?>

                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>

                        <p class="mt-3 text-center">
                            Don't have an account? <a href="register.php">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Bright Future High School</p>
    </footer>

    <!-- Bootstrap 5 JS and Popper.js (for optional components like tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
