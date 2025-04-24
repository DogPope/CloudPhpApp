<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
try{ 
    $db = Database::getInstance();
    $game_id = filter_input(INPUT_POST, 'game_id', FILTER_VALIDATE_INT); // Gets the value from the URL.
    if (!$game_id) {
        throw new Exception("Invalid game_id provided in the URL.");
    }
    $sql = 'DELETE FROM games WHERE game_id = :gid'; // Guess what this does.
    $result = $db->query($sql, [':gid' => $game_id]); // bindValue(), execute() in one method. The recommended way to do it.
    // $row = $result->fetch(); // Don't think this does anything.
    $rowCount = $result->rowCount(); // Check how many rows were affected.
    if ($rowCount > 0) {
        echo "You just deleted Game no: " . $game_id . " \n click<a href='viewUpdateDelete.php'> here</a> to go back.";
    } else {
        echo "No game found with ID: " . $game_id . " \n click<a href='viewUpdateDelete.php'> here</a> to go back.";
    }
}catch(PDOException $e){
    if ($e->getCode() == 23000) {
        echo "That Record has a dependency, and can't be deleted. Click<a href='viewUpdateDelete.php'> here</a> to go back!";
    }
}
?>