<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventory Query</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    label {
        display: block;
        margin-bottom: 10px;
    }
    input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    input[type="submit"] {
        background-color: #3498db;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }

    
</style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#stockcode').on('input', function () {
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
                $('#stockcode').val(suggestion);
                $(this).parent().empty();
            });
        });
    </script>

</head>
<body>
    
<div class="container">
    <h2>Inventory Query</h2>
    <form action="query_result.php" method="post">
        <label for="stockcode">Enter Stock Code:</label>
         <input type="text" name="stockcode" id="stockcode" required>
         <div class="suggestions"></div>
         <input type="submit" value="Search">
         
    </form>



     <!-- Placeholder for displaying query results -->
    <div id="query_results">
        <!-- Results will be displayed here -->
    </div>
   
</div>

    <div class="container">
    <h2>Inventory Import</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="excel" required>
        <button type="submit" name="import">Import</button>
    
    </form>
    <?php

    include 'connection.php'; // Include the connection script
    
    if(isset($_POST["import"])){
        $fileName = $_FILES["excel"]["name"];
        $fileExtension = explode('.', $fileName);
        $fileExtension = strtolower(end($fileExtension));
        $newFileName = date("Y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;

        $targetDirectory = "uploads/" . $newFileName;
        move_uploaded_file($_FILES['excel']['tmp_name'], $targetDirectory);

        require 'excelReader/excel_reader2.php';
        require 'excelReader/SpreadsheetReader.php';

        $reader = new SpreadsheetReader($targetDirectory);
        foreach($reader as $key => $row){
            $stockcode = $row[0];
            $productclass = $row[1];
            $warehouse = $row[2];
            $quantity = $row[3];


          // Prepare the insert statement
            $sql = "INSERT INTO inventory (stockcode, productclass, warehouse, quantity,timestamp) 
                    VALUES (?, ?, ?, ?,NOW())
                    ON DUPLICATE KEY UPDATE 
                    productclass = VALUES(productclass), 
                    warehouse = VALUES(warehouse), 
                    quantity = VALUES(quantity),
                    timestamp = NOW()";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bind_param("sssi", $stockcode, $productclass, $warehouse, $quantity);

            // Execute the statement inside the loop
            foreach ($reader as $key => $row) {
                $stockcode = $row[0];
                $productclass = $row[1];
                $warehouse = $row[2];
                $quantity = $row[3];
                
                // Execute the statement
                if (!$stmt->execute()) {
                    echo "Error inserting data: " . $stmt->error;
                }
            }
                    echo "<script>alert('Successfully Imported');</script>";
                    echo "<script>location.href = '';</script>";
    }
}
   
    ?>
     </div>





</body>
</html>



<?php
include 'connection.php'; // Include the connection script

// Query to retrieve data
$query = "SELECT stockcode, productclass, warehouse, quantity FROM inventory";

// Execute the query
$result = $conn->query($query);

if ($result === false) {
    echo "Error in executing query: " . $conn->error;
    exit;
}


// Fetch results and display them
echo "<table border='1'>
        <tr>
            <th>Stock Code</th>
            <th>productclass</th>
            <th>Warehouse</th>
            <th>Quantity</th>
        </tr>";

// Loop through the results
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['stockcode'] . "</td>";
    echo "<td>" . $row['productclass'] . "</td>";
    echo "<td>" . $row['warehouse'] . "</td>";
    echo "<td>" . $row['quantity'] . "</td>";
    echo "</tr>";
}

echo "</table>";

// Free result and close connection
$result->free();

 $conn->close();
?>


