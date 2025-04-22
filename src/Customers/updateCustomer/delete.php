<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
try{
    include("../../../public/html/header.html");
    $db = Database::getInstance();
    echo "Connection was Successful";
    $sql = 'SELECT count(*) FROM customers where cust_id = :cid';
    $result = $db->prepare($sql);
    $result->bindValue(':cid', $_GET['cust_id']);
    $result->execute();
    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM customers where cust_id = :cid';
        $result = $db->prepare($sql);
        $result->bindValue(':cid', $_GET['cust_id']);
        $result->execute();
        
        while ($row = $result->fetch()){
            echo $row['username'] . ' ' . $row['town'] . ' Are you sure you want to delete?' . '<form action="deleteCustomer.php" method="post">
            <input type="hidden" name="cust_id" value="'.$row['cust_id'].'">
            <input type="submit" value="yes delete" name="delete">
            </form>';
        }
    }else{
        print("No rows matched the query.");
    }
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
    echo $output;
}
?>