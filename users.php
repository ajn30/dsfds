<?php
// Function to add a new user
function addUser($name, $email, $userType, $borrowLimit) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO Users (Name, Email, UserType, BorrowLimit) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $email, $userType, $borrowLimit);
    
    if ($stmt->execute()) {
        echo "New user added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Example usage
addUser("John Doe", "johndoe@example.com", "Student", 3);
?>
