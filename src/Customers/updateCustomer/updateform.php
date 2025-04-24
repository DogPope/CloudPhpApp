<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
try{
    $db = Database::getInstance();
    $cust_id = filter_input(INPUT_GET, 'cust_id', FILTER_VALIDATE_INT);
    if (!$cust_id) {
        throw new Exception("Invalid Customer ID provided in the URL.");
    }
    $sql = "SELECT count(*) FROM customers WHERE cust_id = :cid";
    $result = $db->query($sql, [':cid' => $cust_id]);
    
    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM customers WHERE cust_id = :cid';
        $result = $db->query($sql, [':cid' => $cust_id]);
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
include('updatedetails.html');
?>