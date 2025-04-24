<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include 'header.html';
try{ 
    $db = Database::getInstance();
    $sql = 'SELECT * FROM customers';
    echo "Does this file actually do anything?";
    $result = $db->query($sql); 
    echo "<br /><b>A Quick View</b><br><br>";
    echo "<table border=1>";
    echo "<tr><th>Customer Id</th><th>Fore Name:</th></tr>";
    while ($row = $result->fetch()) {
        echo '<tr><td>' . $row['cust_id'] . '</td><td>'. $row['forename'] . '</td></tr>';
    }
    echo '</table>';
} catch (PDOException $e) { 
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
    echo $output;
}
include 'whotoupdate.html';
?>