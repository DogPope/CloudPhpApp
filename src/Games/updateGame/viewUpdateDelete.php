<?php
require '../../../vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient; 
use Aws\Exception\AwsException;

$client = new SecretsManagerClient([
    'version' => 'latest',
    'region' => 'eu-west-1'
]);

$result = $client->getSecretValue([
    'SecretId' => $_ENV["SECRET_NAME"],
]);

$myJSON = json_decode($result['SecretString']);
// Does this work? I think this might actually work?
define('DB_SERVER', $_ENV["DB_ENDPOINT"]);
define('DB_USERNAME', $myJSON->username);
define('DB_PASSWORD', $myJSON->password);
define('DB_DATABASE', $myJSON->dbname); // FIXED THIS LINE
$dsn = "mysql:host=" . $myJSON->host .
       ";port=" . $myJSON->port .
       ";dbname=" . $myJSON->dbname .
       ";charset=utf8"; // optional but recommended
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