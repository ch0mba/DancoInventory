<?php
include 'connection.php'; // Include the connection script

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete button is clicked
    if (isset($_POST['delete'])) {
        $transactionToDelete = $_POST['stockcode']; // Assuming you have an input field for storing the stock code to delete
        // Delete the records with the specified stock code
        $sqlDelete = "DELETE FROM transactions WHERE stockcode = ?";
        $stmt = $conn->prepare($sqlDelete);
        $stmt->bind_param("s", $transactionToDelete);
        if ($stmt->execute()) {
            echo "Records deleted successfully";
        } else {
            echo "Error deleting records: " . $conn->error;
        }
    } else {
        // Escape user inputs to prevent SQL injection
        $stockcode = $_POST['stockcode'];
        $quantity = $_POST['quantity'];
        $brand = $_POST['brand'];
        $stock_location = $_POST['stock_location'];
        $uom = $_POST['uom'];
        $length = $_POST['length'];
        $comment =$_POST['comment'];


        // Check if the stock code exists in the inventory
        $checkSql = "SELECT * FROM inventory WHERE stockcode = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $stockcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Stock code exists, proceed with the transaction
            $inventoryRow = $result->fetch_assoc();
            $current_quantity = $inventoryRow['quantity'];

            // Escape user inputs to prevent SQL injection
            $transaction_type = $_POST['transaction_type'];

            // Lookup transaction type from transactiontype table
            $lookupSql = "SELECT transaction_type FROM transactiontype WHERE transaction_type = 'Stock Take Override'";
            $stmt = $conn->prepare($lookupSql);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $transaction_type = $row['transaction_type'];

            
                // Insert data into stocktake table
                $sql = "INSERT INTO stocktake (stockcode, brand, stock_location, quantity,uom,length, timestamp, comment) 
                    VALUES (?, ?, ?, ?,?,?, NOW(), ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssisis", $stockcode, $brand, $stock_location, $quantity,$uom,$length, $comment);
        


                if ($stmt->execute()) {
                    echo "New record created successfully";

                    $updateSql = "UPDATE stocktake 
                    SET brand = ?, stock_location = ?, quantity = ?, uom = ?, length = ?, comment = ? 
                    WHERE stockcode = ?";


                    $stmt = $conn->prepare($updateSql);
                    $stmt->bind_param("sssisis",$stockcode, $brand, $stock_location, $quantity, $uom, $length, $comment);

                    if ($stmt->execute()) {
                        echo "Stock take record added successfully";
                    } else {
                        echo "Error inserting record: " . $stmt->error;
                    }
                } else {
                    echo "Transaction type not found";
                }
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

    <title>Transactions </title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#stockcode, #transaction_type').on('input', function () {
                var input = $(this).val();
                var suggestionBox = $(this).siblings('.suggestions');

                $.ajax({
                    url: 'suggestions.php',
                    type: 'GET',
                    data: { input: input },
                    dataType: 'json',
                    success: function (response) {
                        suggestionBox.empty();
                        response.forEach(function (item) {
                            suggestionBox.append('<div class="suggestion">' + item + '</div>');
                        });
                    }
                });
            });

            $(document).on('click', '.suggestion', function () {
                var suggestion = $(this).text();
                $(this).parent().siblings('input').val(suggestion);
                $(this).parent().empty();
            });
        });
    </script>

</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>New Transaction</header>
            <form action="" method="post">
                <!-- Remove ID field from the form -->

                <div class="field input">
                    <label for="stockcode">Stock Code</label>
                    <input type="text" name="stockcode" id="stockcode" required>
                    <div class="suggestions"></div>
                </div>

                <div class="field input">
                    <label for="brand">Brand</label>
                    <input type="text" name="brand" id="brand">
                    <div class="suggestions"></div>
                </div>

                <div class="field input">
                    <label for="transaction_type">Transaction Type</label>
                    <select name="transaction_type" id="transaction_type" required>
                        <option value="Stock Take Override" selected>Stock Take Override</option>
                    </select>
                </div>


                <div class="field input">
                    <label for="quantity">Transaction Quantity</label>
                    <input type="text" name="quantity" id="quantity" required>
                </div>

             <div class="field input">
                    <label for="stock_location">Stock Location</label>
                    <select name="stock_location"  id ="stock_location"required>
                    <?php                
                    // Fetch warehouse  from the Stock location table
                    $sqlStockLoction = "SELECT stock_location FROM stocklocation";
                    $resultStockLocation = $conn->query($sqlStockLoction);
                    if ($resultStockLocation->num_rows > 0) {
                    while ($row = $resultStockLocation->fetch_assoc()) {
                        echo "<option value='" . $row['stock_location'] . "'>" . $row['stock_location'] . "</option>";
                        }
                    }
                   
                    ?>
                    </select>
                    
                </div> 

                <div class="field input">
                    <label for="uom">Unit of Measure</label>
                    <br>
                   <select id="uom" name="uom">
                        <option value=""></option>
                        <option value="kgs">Kgs</option>
                        <option value="mtrs">Meters</option>
                        <option value="bags">Bags</option>
                   </select>
                </div> 
                
                <div class="field input">
                    <label for="length">Length</label>
                    <br>
                    <input type="number" step="0.01" name="length" id="length">
                </div>


                <div class="field input">
                    <label for="comment">Notation</label>
                    <br>
                    <input type="text" name="comment" id="comment">
                </div>

                <div class="field input">
                    <input type="submit" class="btn" name="submit" value="Submit">

                    <!--button type="submit" class="btn" name="delete">Delete</button-->
                    <button id="undo" type="button" class="green-button">Undo</button>
                    <script src="script.js" defer></script>

                    <a href="home.php" class="green-button">Home</a>
                </div>
            </form>
        </div>
    </div>
 </body>
</html>