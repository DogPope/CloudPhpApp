<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
try{
    $db = Database::getInstance();
    $cust_id = filter_input(INPUT_POST, 'cust_id', FILTER_VALIDATE_INT); // Gets the value from the URL.
    if (!$cust_id) {
        throw new Exception("Invalid Customer ID provided in the URL.");
    }
    $sql = 'DELETE FROM customers WHERE cust_id = :cid'; // Guess what this does.
    $result = $db->query($sql, [':cid' => $cust_id]);
    $rowCount = $result->rowCount();
    if ($rowCount > 0) {
        echo "Successfully deleted customer no: " . $_POST['cust_id'] . "<br>";
    } else {
        echo "No customer found with ID: " . $_POST['cust_id'] . "<br>";
    }
    echo("You just deleted customer no: " . $_POST['cust_id'] ." \n click<a href='viewUpdateDelete.php'> here</a> to go back!");
}catch(PDOException $e){
    if ($e->getCode() == 23000) {
        echo "That Record has a dependency, and can't be deleted. Click<a href='viewUpdateDelete.php'> here</a> to go back!";
    }
}
?>