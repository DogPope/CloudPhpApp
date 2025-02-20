<?php
include("../../../public/html/header.html");
try{
    $pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT * FROM customers';
    $result = $pdo->query($sql);

    echo "<br /><b>A Quick View</b><br><br>";
    echo "<table border=1 style='border-collapse:collapse;'>";
    echo "<tr><th>Customer Id</th><th>Forename</th><th>Delete</th><th>Update</th></tr>";

    while($row = $result->fetch()){
        echo '<tr><td>' . $row['cust_id'] . '</td><td>'. $row['forename'] . '</td>';
        echo "<td><a href=\"delete.php?cust_id=".$row['cust_id']."\">Remove</a></td>";
        echo "<td><a href=\"updateform.php?cust_id=".$row['cust_id']."\">Update</a></td>";
        echo "</tr>";
    }
echo '</table>';
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
}
?>