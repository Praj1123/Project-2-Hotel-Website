<?php
// Database configuration
$host = '127.0.0.1';
$db = 'donationDB';
$user = 'root';
$pass = '';


// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Retrieve uniqueId from POST request
$uniqueId = isset($_POST['uniqueId']) ? $conn->real_escape_string($_POST['uniqueId']) : '';

if (empty($uniqueId)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid uniqueId']);
    exit;
}

// Prepare and execute the query
$sql = "SELECT * FROM users WHERE uniqueId = '$uniqueId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch user data
    $userData = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'data' => $userData]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

// Close the connection
$conn->close();
?>


