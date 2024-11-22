<?php
// Start the session
session_start();

// Database connection details
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "cakes"; // Ensure this database exists or create it

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $user = mysqli_real_escape_string($conn, trim($_POST['username']));
    $pass = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Query to check if user exists
    $sql = "SELECT * FROM Users WHERE username='$user'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, fetch user data
        $row = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($pass, $row['password'])) {
            // Password is correct, start session and store user data
            $_SESSION['user_id'] = $row['id']; // Store user ID in session
            $_SESSION['username'] = $row['username']; // Store username in session

            // Redirect to homepage (or any page after successful login)
            header("Location: homepage.php");
            exit;
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "User not found.";
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Bakery Delights</title>
    <style>
        /* Your existing CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Login</button>
            <div class="message">
                <?php if ($message) echo $message; ?>
            </div>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
