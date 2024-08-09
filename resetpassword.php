<?php
session_start();
include 'connection.php'; // Include the connection script

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs to prevent SQL injection
    $username = $_POST['username'];
    $newPassword = $_POST['password'];

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $sql = "UPDATE users1 SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $hashedPassword, $username);

    if ($stmt->execute()) {
        echo "Password reset successfully!";
    } else {
        echo "Failed to reset password. Please try again later.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Reset Password</header>
            <form action="resetpassword.php" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="field input">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" value="Reset" required>
                </div>

                <div class="links">
                    <a href="index.php">Login</a>
                    <br>
                    <a href="register.php">Sign up</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>