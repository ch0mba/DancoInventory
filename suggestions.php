<?php
include 'connection.php'; // Include the connection script

// Fetch stock codes from the inventory table
$sqlStockCodes = "SELECT DISTINCT stockcode FROM inventory";
$resultStockCodes = $conn->query($sqlStockCodes);
$stockCodes = [];
if ($resultStockCodes->num_rows > 0) {
    while ($row = $resultStockCodes->fetch_assoc()) {
        $stockCodes[] = $row['stockcode'];
    }
}

// Fetch transaction types from the transactiontype table
$sqlTransactionTypes = "SELECT DISTINCT transaction_type FROM transactiontype";
$resultTransactionTypes = $conn->query($sqlTransactionTypes);
$transactionTypes = [];
if ($resultTransactionTypes->num_rows > 0) {
    while ($row = $resultTransactionTypes->fetch_assoc()) {
        $transactionTypes[] = $row['transaction_type'];
    }
}

// Combine stock codes and transaction types
$suggestions = array_merge($stockCodes, $transactionTypes);

// Filter suggestions based on user input
$input = $_GET['input'];
$suggestions = array_filter($suggestions, function ($item) use ($input) {
    return stripos($item, $input) !== false;
});

// Limit the suggestions to 5
$suggestions = array_slice($suggestions, 0, 5);

echo json_encode($suggestions);
$conn->close();
?>
