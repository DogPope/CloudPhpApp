<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
include("../../../public/html/header.html");
// This file is the Starting point from update game in the dropdown menu.
// If the form won't display, it's probably a difference in password between the two MySQL versions on laptop and desktop.
echo "Welcome to the Update Section!";
try{
    $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
    echo "Connection was Successful";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT game_id, title, developer, saleprice, quantity FROM Games';
    $result = $pdo->query($sql);

    echo "<br /><b>A Quick View</b><br><br>";
    echo "<table border=1 style='border-collapse:collapse;'>";
    echo "<tr><th>Game Id</th><th>Title:</th><th>Developer</th><th>Sale Price</th><th>Quantity</th><th>Delete</th><th>Update:</th></tr>";

    while($row = $result->fetch()){
        echo '<tr><td>' . $row['game_id'] . '</td><td>'. $row['title'] . '</td><td>'.$row['developer'].'</td><td>'.$row['saleprice'].'</td><td>'.$row['quantity'].'</td>';
        echo "<td><a href=\"delete.php?game_id=".$row['game_id']."\">Remove</a></td>";
        echo "<td><a href=\"updateForm.php?game_id=".$row['game_id']."\">Update</a></td>";
        echo "</tr>";
    }
    echo '</table>';
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}
include("../../../public/html/footer.html");
?>