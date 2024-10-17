<?php

$server = "localhost";
$username = "root";
$password = "mysql"; 
$database = "bluebirdhotel";
$port = 3307;  // The default MySQL port. Change if MySQL is running on a different port.

$conn = mysqli_connect($server, $username, $password, $database, $port);

if (!$conn) {
    die("<script>alert('Connection Failed: ' . mysqli_connect_error());</script>");
} else {
    //echo "<script>alert('Connection Successful.')</script>";
}
