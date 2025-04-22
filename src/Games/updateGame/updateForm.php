<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
try{
    $db = Database::getInstance();
    $sql="SELECT count(*) FROM games WHERE game_id=:gid";
    $result = $db->query($sql);
    $result->bindValue(':gid', $_GET['game_id']);
    $result->execute();

    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM games where game_id=:gid';
        $result = $db->query($sql);
        $result->bindValue(':gid', $_GET['game_id']);
        $result->execute();

        $row = $result->fetch();
        $game_id = $row['game_id'];
        $title = $row['title'];
        $developer = $row['developer'];
        $genre = $row['genre'];
        $saleprice = $row['saleprice'];
        $quantity = $row['quantity'];
        $status = $row['status'];
    }else{
        print "No rows matched the query. try again click<a href='viewUpdateDelete.php'> here</a> to go back";
    }
}catch(PDOException $e) { 
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
    echo $output;
}
include("updateDetails.html");
?>