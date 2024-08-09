<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .box {
        width: 300px;
        height: 200px;
        margin: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .blue { background-color: #3498db; }
    .orange { background-color: #e67e22; }
    .green { background-color: #2ecc71; }
    .pink { background-color: #e74c3c; }
    .purple { background-color:#9b59b6; }
    .yellow { background-color:#c2b300; }
    h2 {
        color: #fff;
        font-size: 24px;
    }
</style>
</head>
<body>

<div class="container">
   <a href="inventory_query.php" class="box blue">
        <h2>Inventory Query</h2>
    </a>
    <a href="inventory_setup.php"class="box orange">
        <h2>Inventory Setup</h2>
    </a>
    <a href="transactions.php"class="box green">
        <h2>Transactions</h2>
    </a>

    <a href="transaction_setup.php"class="box purple ">
        <h2>Transaction Setup</h2>
    </a> 
    <a href="reports.php"class="box pink">
        <h2>Reports</h2>
    </a>
    <a href="logout.php"class="box yellow">
        <h2>Logout</h2>
    </a>
</div>

</body>
</html