<?php
include 'connection.php'; // Include the connection script

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete button is clicked
    if (isset($_POST['delete'])) {
        $warehouseToDelete = $_POST['warehouse']; // Assuming you have an input field for storing the ID of the record to delete
        // Delete the record with the specified ID

        $sqlDelete = "DELETE FROM warehouse WHERE warehouse = '?'"; // Replace 'id' with your actual primary key column name
        if ($conn->query($sqlDelete) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        // Escape user inputs to prevent SQL injection
        $warehouse = $_POST['warehouse'];
        $description = $_POST['description'];
     
        // Check if the stock code already exists
        $sqlCheck = "SELECT * FROM warehouse WHERE warehouse = '$warehouse'";
        $result = $conn->query($sqlCheck);
        if ($result->num_rows > 0) {
            echo "Stock code already exists. Please enter a different stock code.";
        } else {
      
        // Insert data into database
        $sql = "INSERT INTO warehouse (warehouse, description)
                VALUES ('$warehouse', '$description')";
    
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

    <title>Stockcode Setup</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Warehouse Setup</header>
            <form action="" method="post">
                <!-- Remove ID field from the form -->

                <div class="field input">
                    <label for="warehouse">Warehouse</label>
                    <input type="text" name="warehouse" id="warehouse" required>
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