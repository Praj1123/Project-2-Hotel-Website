<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Database connection setup
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "donationDB";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get POST data
$uniqueId = isset($_POST['uniqueId']) ? $_POST['uniqueId'] : '';
$profile = isset($_POST['profile']) ? $_POST['profile'] : '';

// Prepare the SQL statement
$sql = "UPDATE users SET profile = ? WHERE uniqueId=?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement: ' . $conn->error]);
    exit();
}

// Bind parameters and execute
$stmt->bind_param('ss', $profile, $uniqueId); // Correct parameter types

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Record updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
