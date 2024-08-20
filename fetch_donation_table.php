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
$sql = "SELECT * FROM donations WHERE uniqueId = '$uniqueId'";

$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Fetch all rows
        $donations = [];
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $donations]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No donations found for this uniqueId']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to execute query']);
}

// Close the connection
$conn->close();
?>
