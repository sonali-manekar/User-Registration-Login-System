<?php
// Database configuration
$host = 'localhost';
$dbname = 'user_auth';
$username = 'root';
$password = 'Sachin@1998';

// Create database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>