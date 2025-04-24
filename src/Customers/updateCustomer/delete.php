<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
try{
    include("../../../public/html/header.html");
    $db = Database::getInstance();
    $cust_id = filter_input(INPUT_GET, 'cust_id', FILTER_VALIDATE_INT);
    if (!$cust_id){
        throw new Exception("Invalid Customer ID in URL.");
    }
    $sql = 'SELECT count(*) FROM customers where cust_id = :cid';
    $result = $db->query($sql, [':cid' => $cust_id]);
    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM customers where cust_id = :cid';
        $result = $db->query($sql, [':cid' => $cust_id]);
        $row = $result->fetch();
        
        echo "<div>";
        echo "<h3>Delete Confirmation</h3>";
        echo "<p>Customer: <strong>{$row['username']}</strong> by <strong>{$row['email']}</strong></p>";
        echo "<p>Are you sure you want to delete this customer entry?</p>";

        echo "<form action='deletecustomer.php' method='post'>
              <input type='hidden' name='cust_id' value='{$row['cust_id']}'>
              <input type='submit' value='Yes, Delete!' name='delete'>
              <a href='viewUpdateDelete.php'>Cancel</a>
              </form>";
        echo "</div>";
    }else{
        print("No rows matched the query.");
    }
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
    echo $output;
}
?>