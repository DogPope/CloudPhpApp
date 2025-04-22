<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;

include("../../../public/html/header.html");
try{ 
     $db = Database::getInstance();
     $sql = 'DELETE FROM games WHERE game_id = :gid';
     $result = $db->query($sql);
     $result->bindValue(':gid', $_POST['game_id']);
     $result->execute();
     echo "You just deleted Game no: " . $_POST['game_id'] ." \n click<a href='viewUpdateDelete.php'> here</a> to go back ";
}catch(PDOException $e){
     if ($e->getCode() == 23000) {
          echo "ooops couldnt delete as that record is linked to other tables click<a href='viewUpdateDelete.php'> here</a> to go back ";
     }
}
?>