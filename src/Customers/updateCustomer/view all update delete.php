<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
try{
    $db = Database::getInstance();
    $sql = 'SELECT * FROM customers';
    $result = $db->query($sql);

    echo "<br /><b>A Quick View</b><br><br>";
    echo "<table border=1>";
    echo "<tr><th>User Id</th><th>User Name:</th><th>Delete</th><th>Update:</th></tr>";

    while($row = $result->fetch()){
        echo '<tr><td>' . $row['cust_id'] . '</td><td>'. $row['username'] . '</td>';
        echo "<td><a href=\"delete.php?cust_id=".$row['cust_id']."\">Remove</a></td>";
        echo "<td><a href=\"updateform.php?cust_id=".$row['cust_id']."\">Update</a></td>";
        echo "</tr>";
    }
    echo '</table>';
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    echo $output;
}
?>