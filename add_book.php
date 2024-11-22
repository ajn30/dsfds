<?php
// Include the database connection
include('db.php');

// Variables for success or error messages
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Safely check if each form field is set and then assign the values
    $resource_id = isset($_POST['resource_id']) ? $_POST['resource_id'] : '';
    $author = isset($_POST['author']) ? $_POST['author'] : '';
    $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';
    $publisher = isset($_POST['publisher']) ? $_POST['publisher'] : '';
    $edition = isset($_POST['edition']) ? $_POST['edition'] : '';
    $publication_date = isset($_POST['publication_date']) ? $_POST['publication_date'] : '';

    // Debugging: Print the ResourceID to ensure it is being passed correctly
    echo "ResourceID entered: " . $resource_id . "<br>";  // This will print the entered ResourceID

    // Step 1: Check if ResourceID exists in the libraryresources table
    $check_sql = "SELECT 1 FROM libraryresources WHERE ResourceID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $resource_id);  // Assuming ResourceID is an integer
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // ResourceID exists in libraryresources table, proceed with insertion
        echo "ResourceID exists in libraryresources table.<br>"; // Debugging line
        
        $sql = "INSERT INTO Books (ResourceID, Author, ISBN, Publisher, Edition, PublicationDate) 
                VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare and bind the parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $resource_id, $author, $isbn, $publisher, $edition, $publication_date);

        // Execute the statement and check if the insertion was successful
        if ($stmt->execute()) {
            $success_message = 'Book added successfully!';
        } else {
            $error_message = 'Error adding book: ' . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // ResourceID does not exist in the libraryresources table
        $error_message = "Error: ResourceID does not exist in the library resources.";
    }

    // Close the check statement
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book - Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Library Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_user.php">Add User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_book.php">Add Book</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_books.php">View Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="borrow_return.php">Borrow/Return Book</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="report.php">Generate Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="display-4 text-center">Add a New Book</h1>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <form method="POST" action="add_book.php">
                            <!-- Resource ID -->
                            <div class="mb-3">
                                <label for="resource_id" class="form-label">Resource ID</label>
                                <input type="number" name="resource_id" id="resource_id" class="form-control" required>
                            </div>

                            <!-- Author -->
                            <div class="mb-3">
                                <label for="author" class="form-label">Author</label>
                                <input type="text" name="author" id="author" class="form-control" required>
                            </div>

                            <!-- ISBN -->
                            <div class="mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" name="isbn" id="isbn" class="form-control" required>
                            </div>

                            <!-- Publisher -->
                            <div class="mb-3">
                                <label for="publisher" class="form-label">Publisher</label>
                                <input type="text" name="publisher" id="publisher" class="form-control" required>
                            </div>

                            <!-- Edition -->
                            <div class="mb-3">
                                <label for="edition" class="form-label">Edition</label>
                                <input type="text" name="edition" id="edition" class="form-control" required>
                            </div>

                            <!-- Publication Date -->
                            <div class="mb-3">
                                <label for="publication_date" class="form-label">Publication Date</label>
                                <input type="date" name="publication_date" id="publication_date" class="form-control" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">Add Book</button>
                        </form>

                        <!-- Success or Error Message -->
                        <?php if ($success_message): ?>
                            <div class="alert alert-success mt-3"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Bright Future High School</p>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
