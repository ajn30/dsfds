<?php
// Include database connection
include('db.php');

// Fetch books and users from the database for the dropdown lists
$books_sql = "SELECT ResourceID, Author FROM Books"; // Use 'Author' instead of 'Title'
$books_result = $conn->query($books_sql);

$users_sql = "SELECT UserID, Name FROM Users"; // Assuming users have UserID and Name
$users_result = $conn->query($users_sql);

// Variables to store any success or error messages
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];
    $action = $_POST['action'];

    // Check if the action is to borrow or return
    if ($action == 'borrow') {
        // Insert a new borrow transaction into the BorrowTransactions table
        $borrow_sql = "INSERT INTO BorrowTransactions (UserID, BookID, BorrowDate) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($borrow_sql);
        $stmt->bind_param("ii", $user_id, $book_id);

        if ($stmt->execute()) {
            $success_message = 'Book borrowed successfully!';
        } else {
            $error_message = 'Error borrowing book: ' . $stmt->error;
        }

        $stmt->close();
    } elseif ($action == 'return') {
        // Insert a return transaction into the BorrowTransactions table
        $return_sql = "UPDATE BorrowTransactions SET ReturnDate = NOW() WHERE UserID = ? AND BookID = ? AND ReturnDate IS NULL";
        $stmt = $conn->prepare($return_sql);
        $stmt->bind_param("ii", $user_id, $book_id);

        if ($stmt->execute()) {
            $success_message = 'Book returned successfully!';
        } else {
            $error_message = 'Error returning book: ' . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow/Return Book - Library Management System</title>
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
        <div class="text-center mb-4">
            <h1 class="display-4">Borrow or Return a Book</h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Form to borrow or return a book -->
                <div class="card shadow-lg">
                    <div class="card-body">
                        <form method="POST" action="borrow_return.php">
                            <!-- Select User -->
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Select User</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Select User</option>
                                    <?php while ($user = $users_result->fetch_assoc()): ?>
                                        <option value="<?php echo $user['UserID']; ?>"><?php echo $user['Name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Select Book -->
                            <div class="mb-3">
                                <label for="book_id" class="form-label">Select Book</label>
                                <select name="book_id" id="book_id" class="form-select" required>
                                    <option value="">Select Book</option>
                                    <?php while ($book = $books_result->fetch_assoc()): ?>
                                        <option value="<?php echo $book['ResourceID']; ?>"><?php echo $book['Author']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Action (Borrow/Return) -->
                            <div class="mb-3">
                                <label for="action" class="form-label">Action</label>
                                <select name="action" id="action" class="form-select" required>
                                    <option value="">Select Action</option>
                                    <option value="borrow">Borrow</option>
                                    <option value="return">Return</option>
                                </select>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">Submit</button>
                        </form>

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
