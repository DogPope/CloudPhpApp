<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
try{
    $db = Database::getInstance();
    $game_id = filter_input(INPUT_GET, 'game_id', FILTER_VALIDATE_INT);
    if (!$game_id) {
        throw new Exception("Invalid Game ID provided in the URL.");
    }
    $sql = "SELECT count(*) FROM games WHERE game_id = :gid";
    $result = $db->query($sql, [':gid' => $game_id]);
    
    if ($result->fetchColumn() > 0) {
        $sql = 'SELECT * FROM games WHERE game_id = :gid';
        $result = $db->query($sql, [':gid' => $game_id]);
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