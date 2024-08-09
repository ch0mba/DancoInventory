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
        $product_classToDelete = $_POST['product_class']; // Assuming you have an input field for storing the ID of the record to delete
        // Delete the record with the specified ID

        $sqlDelete = "DELETE FROM productclass WHERE product_class = ?"; // Replace 'id' with your actual primary key column name
        if ($conn->query($sqlDelete) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {

        if(isset($_POST['product_class'])){

        // Escape user inputs to prevent SQL injection
        $product_class = $_POST['product_class'];
        $description = $_POST['description'];
     
        // Check if the stock code already exists
        $sqlCheck = "SELECT * FROM productclass WHERE product_class = '$product_class'";
        $result = $conn->query($sqlCheck);
        if ($result->num_rows > 0) {
            echo "Product Class already exists. Please enter a different product_class.";
        } else {
      
        // Insert data into database
        $sql = "INSERT INTO productclass (product_class, description)
                VALUES ('$product_class', '$description')";
    
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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

    <title>Stockcode Setup</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Product Class Setup</header>
            <form action="" method="post">
                <!-- Remove ID field from the form -->

                <div class="field input">
                    <label for="product_class">Product Class</label>
                    <input type="text" name="product_class" id="product_class" required>
                </div>

                <div class="field input">
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description" required>
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