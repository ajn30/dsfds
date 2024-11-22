<?php
// Function to return a book
function returnBook($userID, $resourceID) {
    global $conn;
    
    // Get the transaction details
    $stmt = $conn->prepare("SELECT TransactionID, BorrowDate, DueDate FROM Transactions WHERE UserID = ? AND ResourceID = ? AND ReturnDate IS NULL");
    $stmt->bind_param("ii", $userID, $resourceID);
    $stmt->execute();
    $stmt->bind_result($transactionID, $borrowDate, $dueDate);
    $stmt->fetch();
    
    if (!$transactionID) {
        echo "No active borrowing record found.";
        return;
    }
    
    // Calculate fine if overdue
    $currentDate = date("Y-m-d");
    $fineAmount = 0;
    if ($currentDate > $dueDate) {
        $overdueDays = (strtotime($currentDate) - strtotime($dueDate)) / (60 * 60 * 24);
        $fineAmount = $overdueDays * 0.50; // Example fine rate per day
    }
    
    // Update transaction as returned
    $stmt = $conn->prepare("UPDATE Transactions SET ReturnDate = ?, FineAmount = ? WHERE TransactionID = ?");
    $stmt->bind_param("sdi", $currentDate, $fineAmount, $transactionID);
    
    if ($stmt->execute()) {
        // Update book availability and user's borrowed count
        $stmt = $conn->prepare("UPDATE LibraryResources SET Available = Available + 1 WHERE ResourceID = ?");
        $stmt->bind_param("i", $resourceID);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE Users SET BorrowedBooks = BorrowedBooks - 1 WHERE UserID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        
        echo "Book returned successfully. Fine: $$fineAmount";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Example usage
returnBook(1, 3); // User with ID 1 returns resource with ID 3
?>
