<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;

try { 
    include("../../../public/html/header.html");
    $db = Database::getInstance();
    $sql = 'SELECT count(*) FROM games where game_id = :gid';
    $result = $db->prepare($sql);
    $result->bindValue(':gid', $_GET['game_id']);
    $result->execute();

    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM games where game_id = :gid';
        $result = $db->prepare($sql);
        $result->bindValue(':gid', $_GET['id']); 
        $result->execute();
        
        while ($row = $result->fetch()) { 
            echo $row['title'] . ' ' . $row['developer'] . ' Are you sure you want to delete ??' . '<form action="deleteGame.php" method="post">
                <input type="hidden" name="game_id" value="'.$row['game_id'].'"> 
                <input type="submit" value="Delete!" name="delete">
                </form>';
                //NOTE - Dont keep associative array inside double quote while printing otherwise it would not return any value.     
        }
    }else{
        print "No rows matched the query.";
    }

}catch(PDOException $e){ 
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
    echo $output;
}
?>