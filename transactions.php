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
        $transaction_quantity = $_POST['transaction_quantity'];
        $reference = $_POST['reference'];
        $stakeholer = $_POST['stakeholder'] ?? '';
        $name = $_POST['name'];
        $truck = $_POST['truck'];
        $timein = $_POST['timein'];
        $timeout = $_POST['timeout'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $brand = $_POST['brand'];



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
            $lookupSql = "SELECT transaction_type FROM transactiontype WHERE transaction_type = ?";
            $stmt = $conn->prepare($lookupSql);
            $stmt->bind_param("s", $transaction_type);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $transaction_type = $row['transaction_type'];

                //check if the requested quantity is available for issue or expense transaction
                if(($transaction_type == 'Issue' || 
                    $transaction_type == 'Expense' || 
                    $transaction_type == 'Sample Issuance' ||
                    $transaction_type == 'Departmental Issuance' ||
                    $transaction_type == 'Loss/Pilferage' || 
                    $transaction_type == 'Negative Adjustment' || $transaction_type == 'Sale'   
                
                ) && $transaction_quantity > $current_quantity) {
                    echo "Sorry but only " . $current_quantity . " quantities of stock " . $stockcode . " are available.";
                } else {
                
                // Insert data into transactions table
                $sql = "INSERT INTO transactions 
                        (stockcode, transaction_type, transaction_quantity, reference, stakeholder, name, truck, timein, timeout, start_time, end_time, brand) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssssssss", $stockcode, $transaction_type, $transaction_quantity, $reference, $stakeholder, $name, $truck, $timein, $timeout, $start_time, $end_time, $brand);


                if ($stmt->execute()) {
                    echo "New record created successfully";

                    // Update inventory quantity based on transaction type
                    if ($transaction_type == 'Issue' || $transaction_type == 'Expense' || $transaction_type == 'Negative Adjustment'
                    || $transaction_type == 'Sample Issuance' || $transaction_type == 'Departmental Issuance' || $transaction_type == 'Sale' || $transaction_type == 'Loss/Pilferage') {
                        $updateSql = "UPDATE inventory SET quantity = quantity - ? WHERE stockcode = ?";
                        // positive receipts
                    } elseif ($transaction_type == 'Receipt'  || $transaction_type == 'Positive Adjustment'  || $transaction_type == 'Production Receipt'
                      || $transaction_type == 'Production Receipt' || $transaction_type == 'Customer Returns' || $transaction_type == 'Sample Return' || $transaction_type == 'Transfer In'
                        || $transaction_type == 'Used Spare Receipts' || $transaction_type == 'Bi-product Receipt') 
                    {
                        $updateSql = "UPDATE inventory SET quantity = quantity + ? WHERE stockcode = ?";
                    }

                    $stmt = $conn->prepare($updateSql);
                    $stmt->bind_param("is", $transaction_quantity, $stockcode);

                    if ($stmt->execute()) {
                        echo "Inventory updated successfully";
                    } else {
                        echo "Error updating inventory: " . $conn->error;
                    }
                } else {
                    echo "Error inserting record: " . $conn->error;
                }
            }
            } else {
                echo "Transaction type not found";
            }
        } else {
            echo "Stock code does not exist";
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
                    <input type="text" name="brand" id="brand" required>
                    <div class="suggestions"></div>
                </div>

                <div class="field input">
                    <label for="transaction_type">Transaction Type</label>
                    <select name="transaction_type" id="transaction_type" required>
                    <?php                
                    // Fetch transaction types from the transactiontype table
                    $sqlTransactionTypes = "SELECT transaction_type FROM transactiontype";
                    $resultTransactionTypes = $conn->query($sqlTransactionTypes);
                    if ($resultTransactionTypes->num_rows > 0) {
                    while ($row = $resultTransactionTypes->fetch_assoc()) {
                        echo "<option value='" . $row['transaction_type'] . "'>" . $row['transaction_type'] . "</option>";
                        }
                    }
                    $conn->close();
                    ?>
                    </select>
                </div>

                <div class="field input">
                    <label for="transaction_quantity">Transaction Quantity</label>
                    <input type="text" name="transaction_quantity" id="transaction_quantity" required>
                </div>

                <div class="field input">
                    <label for="stakeholder">Choose Stakeholder</label>
                    <select name="stakeholder" required>
                        <option value="none">None</option>
                        <option value="inventory">Inventory</option>
                        <option value="production">Production</option>
                        <option value="IT">IT</option>
                        <option value="operations">Operations</option>
                    </select>
                    </div>
                    <br>

                <div class="field input">
                    <label for="name">Officer Name</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="field input">
                    <label for="truck">Truck Number</label>
                    <input type="text" name="truck" id="truck" required>
                </div>

                <div class="field input">
                    <label for="timein">Truck Time in</label>
                    <input type="datetime-local" name="timein" id="timein" required>
                </div>

                <div class="field input">
                    <label for="timeout">Truck Time out</label>
                    <input type="datetime-local" name="timeout" id="timeout" required>
                </div>

                <div class="container">
                        <button class="button" type="button" onclick="setTime('start_time')">Start</button>
                        <br>
                        <input type="text" name="start-time" id="start_time" readonly>
                        <br>
                        <button class="button" type="button" onclick="setTime('end_time')">End</button>
                        <br>
                        <input type="text" name="end_time" id="end_time" readonly>
                    </div>

                <div class="field input">
                    <label for="reference">Comments</label>
                    <br>
                    <input type="text" name="reference" id="reference" required>
                </div>

                <div class="field input">
                    <input type="submit" class="btn" name="submit" value="Submit">
                    <!--button type="submit" class="btn" name="delete">Delete</button-->
                    <button id="undo" type="button" class="green-button">Undo</button>
                    <script src="script.js"></script>

                    <a href="home.php" class="green-button">Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>