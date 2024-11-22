<?php
// Enforce HTTPS for secure transmission of cookies
ini_set('session.cookie_secure', 1);

// Start session
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

// Initialize variables for error/success messages
$message = "";

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // Sanitize user input
    $user = mysqli_real_escape_string($conn, trim($_POST['username']));
    $pass = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Validate input lengths
    if (strlen($user) < 3 || strlen($pass) < 6) {
        $message = "Username must be at least 3 characters, and password at least 6 characters.";
    } else {
        // Check if username already exists
        $check_user_query = "SELECT * FROM Users WHERE username='$user'";
        $result = $conn->query($check_user_query);

        if ($result->num_rows > 0) {
            $message = "Username already exists. Please choose a different username.";
        } else {
            // Hash the password
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $sql = "INSERT INTO Users (username, password) VALUES ('$user', '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                $message = "Registration successful! Redirecting to login page...";
                // Redirect to login page after 2 seconds
                header("Refresh: 2; URL=login.php");
                exit;
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
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
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            color: #d9534f;
            text-align: center;
            margin-bottom: 10px;
        }
        .message.success {
            color: #28a745;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
        <h2 style="text-align: center;">User Registration</h2>
        <?php if ($message): ?>
            <div class="message <?php echo (strpos($message, 'successful') !== false) ? 'success' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" minlength="3" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" minlength="6" required>

        <!-- Include CSRF token in the form -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <button type="submit">Register</button>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </form>
</body>
</html>
