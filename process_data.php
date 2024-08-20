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
    die("Connection failed: " . $conn->connect_error);
}

// Read JSON input from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name'], $data['phoneNumber'], $data['amount'], $data['uniqueId'])) {
    // Sanitize input data
    $name = $conn->real_escape_string($data['name']);
    $phoneNumber = $conn->real_escape_string($data['phoneNumber']);
    $amount = $conn->real_escape_string($data['amount']);
    $uniqueId = $conn->real_escape_string($data['uniqueId']);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO donations (uniqueId, name, phoneNumber, donatedAmount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $uniqueId, $name, $phoneNumber, $amount);

    // Execute the statement
    if ($stmt->execute()) {
        $response = ['status' => 'success', 'message' => 'Data stored successfully'];
    } else {
        $response = ['status' => 'error', 'message' => $stmt->error];
    }

    // Close the statement
    $stmt->close();
} else {
    $response = ['status' => 'error', 'message' => 'Invalid input'];
}

// Close the connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
