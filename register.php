<?php
session_start();
require_once 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    $errors = [];
    
    // Validate username
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long";
    }
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    // Validate password
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['register_error'] = "Username already exists";
                header("Location: ../Urls.html");
                exit();
            }
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['register_error'] = "Email already exists";
                header("Location: ../Urls.html");
                exit();
            }
            
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->execute();
            
            $_SESSION['register_success'] = "Registration successful! You can now login.";
            header("Location: ../Urls.html");
            exit();
            
        } catch(PDOException $e) {
            $_SESSION['register_error'] = "Registration failed: " . $e->getMessage();
            header("Location: ../Urls.html");
            exit();
        }
    } else {
        $_SESSION['register_error'] = implode("<br>", $errors);
        header("Location: ../Urls.html");
        exit();
    }
} else {
    // If not a POST request, redirect to the registration page
    header("Location: ../Urls.html");
    exit();
}
?>