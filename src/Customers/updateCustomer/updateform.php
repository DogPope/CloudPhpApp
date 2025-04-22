<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");

// Takes in 'cust_id' from view all update delete.php
try{
    $db = Database::getInstance();
    echo "Connection was Successful";

    $sql="SELECT count(*) FROM customers WHERE cust_id=:cid";

    $result = $db->prepare($sql);
    $result->bindValue(':cid', $_GET['cust_id']);
    $result->execute();
    
    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM customers where cust_id = :cid';
        $result = $db->prepare($sql);
        $result->bindValue(':cid', $_GET['cust_id']);
        $result->execute();

        $row = $result->fetch();
        $cust_id = $row['cust_id'];
        $username = $row['username'];
        $town = $row['town'];
        $eircode = $row['eircode'];
        $password = $row['password'];
        $phone = $row['phone'];
        $email = $row['email'];
        $cardnumber = $row['cardnumber'];
        $status = $row['status'];
        $county = $row['county'];
    }else{
        print "No rows matched the query. try again click<a href='viewUpdateDelete.php'> here</a> to go back";
    }
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    echo $output;
}
include 'updateDetails.html';
?>