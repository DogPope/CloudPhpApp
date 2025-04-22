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
try{
    $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
    echo "Connection was Successful";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT * FROM customers';
    $result = $pdo->query($sql);

    echo "<br /><b>A Quick View</b><br><br>";
    echo "<table border=1>";
    echo "<tr><th>User Id</th><th>User Name:</th><th>Delete</th><th>Update:</th></tr>";

    while($row = $result->fetch()){
        echo '<tr><td>' . $row['cust_id'] . '</td><td>'. $row['username'] . '</td>';
        echo "<td><a href=\"delete.php?cust_id=".$row['cust_id']."\">Remove</a></td>";
        echo "<td><a href=\"updateform.php?cust_id=".$row['cust_id']."\">Update</a></td>";
        echo "</tr>";
    }
    echo '</table>';
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}
?>