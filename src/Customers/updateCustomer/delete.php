<?php

try { 
    include("../../../public/html/header.html");
$pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', ''); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = 'SELECT count(*) FROM customers where cust_id = :cid';
$result = $pdo->prepare($sql);
$result->bindValue(':cid', $_GET['cust_id']);
$result->execute();

if($result->fetchColumn() > 0) 
{
    $sql = 'SELECT * FROM customers where cust_id = :cid';
    $result = $pdo->prepare($sql);
    $result->bindValue(':cid', $_GET['cust_id']); 
    $result->execute();
    
while ($row = $result->fetch()) { 
      
      echo $row['forename'] . ' ' . $row['town'] . ' Are you sure you want to delete?' . '<form action="deleteCustomer.php" method="post">
            <input type="hidden" name="cust_id" value="'.$row['cust_id'].'"> 
            <input type="submit" value="yes delete" name="delete">
        </form>';
   }
}
else {
      print "No rows matched the query.";
    }} 
catch (PDOException $e) { 
$output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
}



?>