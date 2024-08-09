<?php
session_start();
include 'connection.php'; // Include the connection script

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs to prevent SQL injection
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the provided credentials are valid
    $sql = "SELECT * FROM users1 WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['department'] = $row['department']; // Assuming 'department' is a column in your 'users' table
            // Redirect to a success page or perform other actions
            header("Location: home.php");
            exit;
        } else {
            echo "Invalid username or password";
        }
    } else {
        echo "Invalid username or password";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username - Danco Number</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>

                <div class="links">
                    <a href="register.php">Sign up</a><br>
                    <a href="resetpassword.php">Forgot password?</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>