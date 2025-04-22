<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
try{
    $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
    echo "Connection was Successful";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'DELETE FROM customers WHERE cust_id = :cid';
    $result = $pdo->prepare($sql);
    $result->bindValue(':cid', $_POST['cust_id']);
    $result->execute();
    echo("You just deleted customer no: " . $_POST['cust_id'] ." \n click<a href='viewUpdateDelete.php'> here</a> to go back!");
}catch(PDOException $e){
    if ($e->getCode() == 23000) {
        echo "ooops couldnt delete as that record is linked to other tables click<a href='viewUpdateDelete.php'> here</a> to go back ";
    }
}
?>