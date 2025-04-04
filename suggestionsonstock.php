// Check if warehouse exists
$checkSql = "SELECT * FROM warehouse WHERE warehouse = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("s", $warehouse);
$stmt->execute();
$resultWarehouse = $stmt->get_result();

if ($resultWarehouse->num_rows > 0) {
    // Warehouse exists, fetch available stock codes for this warehouse
    $stmt->close();

    $sqlStockCodes = "SELECT DISTINCT stockcode FROM inventory WHERE warehouse = ?";
    $stmt = $conn->prepare($sqlStockCodes);
    $stmt->bind_param("s", $warehouse);
    $stmt->execute();
    $resultStockCodes = $stmt->get_result();

    $stockCodes = [];
    if ($resultStockCodes->num_rows > 0) {
        while ($row = $resultStockCodes->fetch_assoc()) {
            $stockCodes[] = $row['stockcode'];
        }
    }

    // Output stock codes as a suggestion list
    if (!empty($stockCodes)) {
        echo "Available stock codes for warehouse $warehouse: " . implode(", ", $stockCodes);
    } else {
        echo "No stock codes available for warehouse $warehouse.";
    }

    // Close statement
    $stmt->close();
} else {
    echo "Warehouse does not exist.";
}

// Close the database connection
$conn->close();
