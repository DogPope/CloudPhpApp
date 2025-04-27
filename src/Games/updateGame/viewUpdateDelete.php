<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
// This file is the Starting point from update game in the dropdown menu.
header('Content-Type: application/json');
try {
    $db = Database::getInstance();
    $sql = 'SELECT game_id, title, developer, saleprice, quantity FROM games';
    $result = $db->query($sql);
    echo "<br /><b>A Quick View</b><br><br>";
    echo "<table border=1 style='border-collapse:collapse;'>";
    echo "<tr><th>Game Id</th><th>Title:</th><th>Developer</th><th>Sale Price</th><th>Quantity</th><th>Delete</th><th>Update:</th></tr>";

    while ($row = $result->fetch()) {
        $gameId = $row['game_id']; 
        echo '<tr>';
        echo '<td>' . $gameId . '</td>';
        echo '<td>' . $row['title'] . '</td>';
        echo '<td>' . $row['developer'] . '</td>';
        echo '<td>' . $row['saleprice'] . '</td>';
        echo '<td>' . $row['quantity'] . '</td>';
        echo '<td><a href="delete.php?game_id=' . $gameId . '">Remove</a></td>';
        echo '<td><a href="updateForm.php?game_id=' . $gameId . '">Update</a></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo json_encode(['games' => $games]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    echo $output;
}
include("../../../public/html/footer.html");
?>