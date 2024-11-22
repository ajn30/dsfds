<?php
// Function to borrow a book
function borrowBook($userID, $resourceID) {
    global $conn;
    
    // Check if the user can borrow more books
    $stmt = $conn->prepare("SELECT BorrowedBooks, BorrowLimit FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($borrowedBooks, $borrowLimit);
    $stmt->fetch();
    
    if ($borrowedBooks >= $borrowLimit) {
        echo "User has reached their borrowing limit.";
        return;
    }
    
    // Get the current availability of the book
    $stmt = $conn->prepare("SELECT Available FROM LibraryResources WHERE ResourceID = ?");
    $stmt->bind_param("i", $resourceID);
    $stmt->execute();
    $stmt->bind_result($available);
    $stmt->fetch();
    
    if ($available <= 0) {
        echo "Book is currently unavailable.";
        return;
    }
    
    // Proceed with borrowing
    $dueDate = date("Y-m-d", strtotime("+14 days"));
    $stmt = $conn->prepare("INSERT INTO Transactions (UserID, ResourceID, DueDate) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userID, $resourceID, $dueDate);
    
    if ($stmt->execute()) {
        // Update the book's availability and user's borrowed books count
        $stmt = $conn->prepare("UPDATE LibraryResources SET Available = Available - 1 WHERE ResourceID = ?");
        $stmt->bind_param("i", $resourceID);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE Users SET BorrowedBooks = BorrowedBooks + 1 WHERE UserID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        
        echo "Book borrowed successfully. Due date: $dueDate";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Example usage
borrowBook(1, 3); // User with ID 1 borrows resource with ID 3
?>
