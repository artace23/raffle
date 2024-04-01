<?php
include "connect.php";
// Check if the POST data contains the winner's name
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"])) {
    $name = $_POST["name"];
    $timestamp = date('Y-m-d H:i:s');

    // Prepare the SQL statement to insert data into the winners table
    $stmt = $conn->prepare("INSERT INTO winners (name, timestamp) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $timestamp);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "Winner's data saved successfully.";
    } else {
        echo "Error saving winner's dataaaaa: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No data received.";
}

// Close connection
$conn->close();
?>