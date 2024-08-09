<?php
include 'connection.php'; // Include the connection script

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete button is clicked
    if (isset($_POST['delete'])) {
        $stockcodeToDelete = $_POST['stockcode']; // Assuming you have an input field for storing the ID of the record to delete
        // Delete the record with the specified ID
        $sqlDelete = "DELETE FROM inventory WHERE stockcode = $stockcode"; // Replace 'id' with your actual primary key column name
        if ($conn->query($sqlDelete) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        // Escape user inputs to prevent SQL injection
        $stockcode = $_POST['stockcode'];
        $productclass = $_POST['productclass'];
        $warehouse = $_POST['warehouse'];
        $quantity = $_POST['quantity'];

        // Lookup transaction type from warehouse table
        $lookupSql = "SELECT * FROM warehouse WHERE warehouse = ?";
        $stmt = $conn->prepare($lookupSql);
        $stmt->bind_param("s", $warehouse);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $warehouse = $row['warehouse']; // Assuming there's a separate variable for the warehouse name
            // Now you have the warehouse name available in $warehouse
        }

        // Check if the stock code already exists
        $sqlCheck = "SELECT * FROM inventory WHERE stockcode = '$stockcode'";
        $result = $conn->query($sqlCheck);
        if ($result->num_rows > 0) {
            echo "Stock code already exists. Please enter a different stock code.";
        } else {
      
        // Insert data into database
        $sql = "INSERT INTO inventory (stockcode, productclass, warehouse, quantity)
                VALUES ('$stockcode', '$productclass', '$warehouse', '$quantity')";
    
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
   }
}



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
            <header>Stockcode Setup</header>
            <form action="" method="post">
                <!-- Remove ID field from the form -->

                <div class="field input">
                    <label for="stockcode">Stock Code</label>
                    <input type="text" name="stockcode" id="stockcode" required>
                </div>


                <div class="field input">
                    <label for="productclass">Product Class</label>
                    <select name="productclass" id="productclass" required>
                        <?php                
                        // Fetch product_class from the productclass table
                        $sqlProductClass = "SELECT product_class FROM productclass"; 
                        $resultProductClass = $conn->query($sqlProductClass);
                        if ($resultProductClass->num_rows > 0) {
                        while ($row = $resultProductClass->fetch_assoc()) {
                                echo "<option value='" . $row['product_class'] . "'>" . $row['product_class'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>


                <div class="field input">
                    <label for="warehouse">Warehouse</label>
                    <select name="warehouse" id="warehouse" required>
                        <?php                
                        // Fetch product_class from the productclass table
                        $sqlWarehouse = "SELECT warehouse FROM warehouse"; 
                        $resultWarehouse = $conn->query($sqlWarehouse);
                        if ($resultWarehouse->num_rows > 0) {
                        while ($row = $resultWarehouse->fetch_assoc()) {
                                echo "<option value='" . $row['warehouse'] . "'>" . $row['warehouse'] . "</option>";
                            }
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>



                <div class="field input">
                    <label for="quantity">Quantity</label>
                    <input type="text" name="quantity" id="quantity" required>
                </div>


                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Submit">
                   
                    <button id="undo" type="button" class="btn">Undo</button>
                    <script src="script.js"></script>

                

                    <a href="home.php" class="btn">Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>