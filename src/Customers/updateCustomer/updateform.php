<?php
include("../../../public/html/header.html");

// Takes in 'cust_id' from view all update delete.php
try{
    $pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="SELECT count(*) FROM customers WHERE cust_id=:cid";

    $result = $pdo->prepare($sql);
    $result->bindValue(':cid', $_GET['cust_id']);
    $result->execute();
    
    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM customers where cust_id = :cid';
        $result = $pdo->prepare($sql);
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
}
include 'updateDetails.html';
?>