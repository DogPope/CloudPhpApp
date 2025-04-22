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
    $sql = 'DELETE FROM customers WHERE cust_id = :cid';
    $result = $pdo->prepare($sql);
    $result->bindValue(':cid', $_POST['cust_id']);
    $result->execute();
    echo("You just deleted customer no: " . $_POST['cust_id'] ." \n click<a href='viewUpdateDelete.php'> here</a> to go back!");
}catch(PDOException $e){
    if ($e->getCode() == 23000) {
        echo "ooops couldnt delete as that record is linked to other tables click<a href='viewUpdateDelete.php'> here</a> to go back ";
    }
}
?>