<?php
// Include database connection
include('db.php');
session_start();

if (isset($_POST['register'])) {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userType = $_POST['userType'];
    
    // Hash the password before saving it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO Users (Name, Email, UserType, Password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $userType, $hashedPassword);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Library Management System</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <header class="bg-primary text-white text-center py-4">
        <h1>Create an Account</h1>
    </header>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Register</h5>

                        <!-- Registration Form -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="userType" class="form-label">User Type</label>
                                <select name="userType" id="userType" class="form-select" required>
                                    <option value="Student">Student</option>
                                    <option value="Faculty">Faculty</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>

                            <?php if (isset($error)) { ?>
                                <div class="alert alert-danger mt-3">
                                    <?php echo $error; ?>
                                </div>
                            <?php } ?>

                            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                        </form>

                        <p class="mt-3 text-center">
                            Already have an account? <a href="login.php">Login here</a>
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
