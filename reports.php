<?php
include 'connection.php'; // Include the connection script

// Check if a transaction date is provided
if(isset($_POST["transaction_date"])) {
    // Get the transaction date
    $transactionDate = $_POST["transaction_date"];

    // SQL query to retrieve data based on the transaction date
    $sql = "SELECT 
                t.stockcode,
                i.quantity,
                t.transaction_type,
                t.transaction_date,
                t.transaction_quantity AS transaction_quantity,
                CASE 
                    WHEN t.transaction_type IN ('Issue', 'Expense') THEN i.quantity + t.transaction_quantity
                    WHEN t.transaction_type = 'Receipt' THEN i.quantity - t.transaction_quantity
                END AS previous_balance,
                t.reference
            FROM 
                transactions t
            JOIN 
                inventory i ON t.stockcode = i.stockcode
            WHERE 
                DATE(t.transaction_date) = ?";
    
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Check if the SQL statement was prepared successfully
    if ($stmt) {
        $stmt->bind_param("s", $transactionDate);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there are results
        if ($result->num_rows > 0) {
            // Output table header
            echo "<table id='transactionTable' border='1'>
                    <tr>
                        <th>Stock Code</th>
                        <th>Quantity on Hand</th>
                        <th>Transaction Type</th>
                        <th>Transaction Date</th>
                        <th>Transaction Quantity</th>
                        <th>Previous Balance</th>
                        <th>Reference</th>
                    </tr>";
            
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["stockcode"] . "</td>";
                echo "<td>" . $row["quantity"] . "</td>";
                echo "<td>" . $row["transaction_type"] . "</td>";
                echo "<td>" . $row["transaction_date"] ."</td>";
                echo "<td>" . $row["transaction_quantity"] . "</td>";
                echo "<td>" . $row["previous_balance"] . "</td>";
                echo "<td>" . $row["reference"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No results found for the provided transaction date.";
        }

        // Free result
        $result->free();
    } else {
        // Handle error if SQL statement preparation fails
        echo "Error preparing SQL statement: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report</title>
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

        .container{
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 90vh;
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

        .form-box header{
            font-size: 25px;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom:1px solid #e6e6e6;
            margin-bottom: 10px;
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
    <div class = "container" >
        <div class = "box form-box">
            <header>Transaction Report</header>
            <form action="" method="post">
                <label for="transaction_date">Transaction Date:</label>
                <input type="date" name="transaction_date" id="transaction_date" required>
                <input class="btn" type="submit" value="Generate Report">
             </form>

            
                <!-- JavaScript function to print the table -->
              <script>
                function printTable() {
                    var printWindow = window.open('', '_blank');
                    printWindow.document.write('<html><head><title>Print Table</title></head><body>');
                    printWindow.document.write(document.getElementsByTagName('table')[0].outerHTML);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.print();
                }
              </script>

            <!-- Button to trigger printing of the table -->
            <button class= "btn"onclick="printTable()">Print Table</button>
        

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
                  <button class="export-btn" onclick="exportToCsv()">Export Transaction Report to CSV</button>
        </div>

    </div>
</body>
</html>
