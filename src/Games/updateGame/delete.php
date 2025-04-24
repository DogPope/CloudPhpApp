<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");

try {
    $db = Database::getInstance();
    $game_id = filter_input(INPUT_GET, 'game_id', FILTER_VALIDATE_INT);
    if (!$game_id) {
        throw new Exception("Invalid game_id provided in the URL.");
    }
    $sql = 'SELECT count(*) FROM games WHERE game_id = :gid';
    $result = $db->query($sql, [':gid' => $game_id]);

    if ($result->fetchColumn() > 0) {
        $sql = 'SELECT * FROM games WHERE game_id = :gid';
        $result = $db->query($sql, [':gid' => $game_id]);
        $row = $result->fetch();

        echo "<div class='delete-confirmation'>";
        echo "<h3>Delete Confirmation</h3>";
        echo "<p>Game: <strong>{$row['title']}</strong> by <strong>{$row['developer']}</strong></p>";
        echo "<p>Are you sure you want to delete this game?</p>";

        echo "<form action='deleteGame.php' method='post'>
              <input type='hidden' name='game_id' value='{$row['game_id']}'>
              <input type='submit' value='Yes, Delete!' name='delete' class='delete-btn'>
              <a href='viewUpdateDelete.php' class='cancel-link'>Cancel</a>
              </form>";
        echo "</div>";
    } else {
        echo "<p>No game found with that ID. <a href='viewUpdateDelete.php'>Return to list</a></p>";
    }
}catch(PDOException $e){ 
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
    echo $output;
}
?>