<?php

include "connect.php";

// Check if name and department are set and not empty
if (isset($_POST['name']) && !empty($_POST['name'])) {
    $name = $_POST['name'];
    $department = "N/A";

    // SQL query to insert data into the database
    $sql = "INSERT INTO employee (name, department) VALUES (?, ?)";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("ss", $name, $department);

    // Execute the prepared statement
    if ($stmt->execute()) {
        // Insertion successful
        echo "Name inserted successfully!";
    } else {
        // Insertion failed
        echo "Error: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle the case where name or department is not set or empty
    echo "Error: Name and department are required.";
}

// Close the connection
$conn->close();

