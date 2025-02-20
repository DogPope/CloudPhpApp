<?php
include("../../../public/html/header.html");
try{ 
$pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', ''); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = 'DELETE FROM games WHERE game_id = :gid';
$result = $pdo->prepare($sql);
$result->bindValue(':gid', $_POST['game_id']);
$result->execute();
echo "You just deleted Game no: " . $_POST['game_id'] ." \n click<a href='viewUpdateDelete.php'> here</a> to go back ";                                                                 
}catch(PDOException $e){ 
     if ($e->getCode() == 23000) {
          echo "ooops couldnt delete as that record is linked to other tables click<a href='viewUpdateDelete.php'> here</a> to go back ";
     }
}
?>