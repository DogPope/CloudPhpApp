<?php
include '../header.html';

   try {
$pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', ''); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = 'SELECT game_id, title, developer, saleprice, quantity FROM Games';
$result = $pdo->query($sql); 

echo "<br /><b>A Quick View</b><br><br>";
echo "<table border=1 style='border-collapse:collapse;'>";
echo "<tr><th>Game Id</th><th>Title:</th><th>Developer</th><th>Sale Price</th><th>Quantity</th><th>Delete</th><th>Update:</th></tr>";


while ($row = $result->fetch()) {
echo '<tr><td>' . $row['game_id'] . '</td><td>'. $row['title'] . '</td><td>'.$row['developer'].'</td><td>'.$row['saleprice'].'</td><td>'.$row['quantity'].'</td>';
echo "<td><a href=\"delete.php?game_id=".$row['game_id']."\">Remove</a></td>";
      echo "<td><a href=\"updateForm.php?game_id=".$row['game_id']."\">Update</a></td>";
      echo "</tr>";




}
echo '</table>';
} 
catch (PDOException $e) { 
$output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
}
?>