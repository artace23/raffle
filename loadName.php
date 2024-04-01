<?php

include "connect.php";

$sql = "SELECT * FROM employee";

$stmt = $conn->query($sql);

$result = $stmt->fetch_all(MYSQLI_ASSOC);

echo json_encode($result);


$conn->close();
    
