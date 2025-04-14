<?php
session_start();
require_once 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both username and password";
        header("Location: ../Urls.html");
        exit();
    }
    
    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                session_regenerate_id();
                
                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Password is incorrect
                $_SESSION['login_error'] = "Invalid username or password";
                header("Location: ../Urls.html");
                exit();
            }
        } else {
            // User does not exist
            $_SESSION['login_error'] = "Invalid username or password";
            header("Location: ../Urls.html");
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['login_error'] = "Login failed: " . $e->getMessage();
        header("Location: ../Urls.html");
        exit();
    }
} else {
    // If not a POST request, redirect to the login page
    header("Location: ../Urls.html");
    exit();
}
?>