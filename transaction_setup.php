<?php
session_start(); // Start the session
include 'connection.php'; // Include the connection script

// Check if the user is authenticated
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirect to login page if not authenticated
    exit();
}

// Check if the user belongs to the IT department
$authorized_departments = ["IT"]; // List of authorized departments
$user_department = $_SESSION['department']; // Assuming department information is stored in session

if (!in_array($user_department, $authorized_departments)) {
    echo "You are not authorized to access this page.";
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete button is clicked
    if (isset($_POST['delete'])) {
        $idToDelete = $_POST['idToDelete']; // Assuming you have an input field for storing the ID of the record to delete
        // Delete the record with the specified ID
        $sqlDelete = "DELETE FROM transactiontype WHERE transaction_type = $transactionToDelete"; // Replace 'id' with your actual primary key column name
        if ($conn->query($sqlDelete) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        // Escape user inputs to prevent SQL injection
        $transaction_type = $_POST['transaction_type'];
        
        // Check if the stock code already exists
        $sqlCheck = "SELECT * FROM transactiontype WHERE transaction_type = '$transaction_type'";
        $result = $conn->query($sqlCheck);
        if ($result->num_rows > 0) {
            echo "transaction type already exists. Please enter a different transaction type.";
        } else {
      
        // Insert data into database
        $sql = "INSERT INTO transactiontype (transaction_type)
                VALUES ('$transaction_type')";
    
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
   }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">

    <title>Transaction Setup</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Transaction Type Setup</header>
            <form action="" method="post">
                <!-- Remove ID field from the form -->

                <div class="field input">
                    <label for="transaction">Transaction Type</label>
                    <input type="text" name="transaction_type" id="transaction_type" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Submit">
                    <button type="button" class="btn">Delete</button>

                    <button id="undo" type="button" class="btn">Undo</button>
                     <script src="script.js"></script>

                

                    <a href="home.php" class="btn">Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>