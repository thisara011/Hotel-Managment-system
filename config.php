<?php
// Database connection parameters
$host = 'localhost';
$user = 'root'; //  database username
$password = ''; //   database password
$dbname = 'bluebirdhotel'; //  database name

try {
    // Create a new mysqli connection
    $conn = new mysqli($host, $user, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        // Throw an exception if there is a connection error
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }

    //echo "Connected successfully to the database.";

} catch (Exception $e) {
    // Handle the exception
    echo "Error: " . $e->getMessage();
}
?>
