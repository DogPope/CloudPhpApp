<?php
require '../../../vendor/autoload.php';

use App\Core\Database;

include '../../../public/html/header.html';

if (isset($_POST['submitdetails'])) {
    try {
        $title = $_POST['title'];
        $developer = $_POST['developer'];
        $genre = $_POST['genre'];
        $saleprice = $_POST['saleprice'];
        $quantity = $_POST['quantity'];
        if ($title == '' or $developer == '') {
            echo("You did not complete the insert form correctly <br> ");
        } else {
            $db = Database::getInstance();
            
            $gameId = $db->insert('games', [
                'title' => $title,
                'developer' => $developer,
                'genre' => $genre,
                'saleprice' => $saleprice,
                'quantity' => $quantity
            ]);
            
            if ($gameId) {
                echo "Game added successfully with ID: $gameId";
            } else {
                echo "Error adding game";
            }
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
include("addGame.html");
include('../../../public/html/footer.html');
?>