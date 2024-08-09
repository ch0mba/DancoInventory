<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Details</title>
    <style>
        /* CSS styles for table and button */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        h3 {
            margin-top: 30px;
        }

        .export-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }


        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: rgba(255,140,0,0.808);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Filter form styles */
        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form label {
            margin-right: 10px;
        }

        .filter-form input[type="text"] {
            padding: 5px;
            margin-right: 10px;
        }

        .filter-form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    include 'connection.php'; // Include the connection script

    // Function to sanitize input data
    function sanitize($conn, $data) {
        return mysqli_real_escape_string($conn, trim($data));
    }

    // Check if stock code is provided in the request
    if (isset($_POST['stockcode'])) {
        $stockCode = sanitize($conn, $_POST['stockcode']);

        // Query to retrieve inventory details
        $inventoryQuery = "SELECT stockcode, productclass, warehouse, quantity FROM inventory WHERE stockcode = '$stockCode'";
        $inventoryResult = $conn->query($inventoryQuery);

        if ($inventoryResult === false) {
            echo "Error in executing inventory query: " . $conn->error;
            exit;
        }

        // Fetch inventory results and display them
        echo "<h3>Inventory Details</h3>";
        echo "<table id='inventoryTable' border='1'>
            <tr>
                <th>Stock Code</th>
                <th>Product Class</th>
                <th>Warehouse</th>
                <th>Quantity</th>
            </tr>";

        // Loop through the inventory results
        while ($row = $inventoryResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['stockcode'] . "</td>";
            echo "<td>" . $row['productclass'] . "</td>";
            echo "<td>" . $row['warehouse'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Free inventory result
        $inventoryResult->free();

        // Display filter form for transaction table
        echo "<h3>Transaction History</h3>";
        echo "<div class='filter-form'>
                    <form action='' method='post'>
                        <label for='transaction_date'>Transaction Date:</label>
                        <input type='text' name='transaction_date' id='transaction_date' placeholder='YYYY-MM-DD'>
                        <button type='submit'>Filter</button>
                    </form>
                  </div>";

        // Query to retrieve transaction history
        $transactionQuery = "SELECT stockcode, transaction_date, transaction_type, transaction_quantity, reference FROM transactions WHERE stockcode = '$stockCode'";

        // Filter condition for transaction date
        if (isset($_POST['transaction_date']) && !empty($_POST['transaction_date'])) {
            $transactionDate = sanitize($conn, $_POST['transaction_date']);
            $transactionQuery .= " AND DATE(transaction_date) = '$transactionDate'";
        }

        $transactionResult = $conn->query($transactionQuery);

        if ($transactionResult === false) {
            echo "Error in executing transaction query: " . $conn->error;
            exit;
        }

        // Fetch transaction history results and display them
        echo "<table id='transactionTable' border='1'>
                    <tr>
                        <th>Stockcode</th>
                        <th>Transaction Date</th>
                        <th>Transaction Type</th>
                        <th>Transaction Quantity</th>
                        <th>Reference</th>
                    </tr>";

        // Loop through the transaction history results
        while ($row = $transactionResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['stockcode'] . "</td>";
            echo "<td>" . $row['transaction_date'] . "</td>";
            echo "<td>" . $row['transaction_type'] . "</td>";
            echo "<td>" . $row['transaction_quantity'] . "</td>";
            echo "<td>" . $row['reference'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Free transaction history result
        $transactionResult->free();
    } else {
        echo "No stock code provided.";
    }

    // Close connection
    $conn->close();
    ?>

    <!-- JavaScript function to print the table -->
    <script>
        function printInventoryTable() {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Inventory Table</title></head><body>');
            printWindow.document.write(document.getElementById('inventoryTable').outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        function printTransactionTable() {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Transaction Table</title></head><body>');
            printWindow.document.write(document.getElementById('transactionTable').outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>

    <!-- Button to trigger printing of the inventory table -->
    <button  class ="btn" onclick="printInventoryTable()">Print Inventory Table</button>

    <!-- Button to trigger printing of the transaction table -->
    <button  class= "btn" onclick="printTransactionTable()">Print Transaction Table</button>

    <!-- JavaScript function to export transaction table data to CSV -->
    <script>
        function exportToCsv() {
            var csv = [];
            var rows = document.getElementById('transactionTable').querySelectorAll('tr');

            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll('td, th');

                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);

                csv.push(row.join(','));
            }

            var csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'transaction_table.csv');
            document.body.appendChild(link);
            link.click();
        }
    </script>

    <!-- Button to export transaction table data to CSV -->
    <button class="export-btn" onclick="exportToCsv()">Export Transaction Table to CSV</button>
</div>
</body>
</html>
