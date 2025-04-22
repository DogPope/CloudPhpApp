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
define('DB_SERVER', $_ENV["DB_ENDPOINT"]);
define('DB_USERNAME', $myJSON->username);
define('DB_PASSWORD', $myJSON->password);
define('DB_DATABASE', $myJSON->dbname); // FIXED THIS LINE
$dsn = "mysql:host=" . $myJSON->host .
       ";port=" . $myJSON->port .
       ";dbname=" . $myJSON->dbname .
       ";charset=utf8"; // optional but recommended
include("../../../public/html/header.html");

// Takes in 'cust_id' from view all update delete.php
try{
    $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
    echo "Connection was Successful";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql="SELECT count(*) FROM customers WHERE cust_id=:cid";

    $result = $pdo->prepare($sql);
    $result->bindValue(':cid', $_GET['cust_id']);
    $result->execute();
    
    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM customers where cust_id = :cid';
        $result = $pdo->prepare($sql);
        $result->bindValue(':cid', $_GET['cust_id']);
        $result->execute();

        $row = $result->fetch();
        $cust_id = $row['cust_id'];
        $username = $row['username'];
        $town = $row['town'];
        $eircode = $row['eircode'];
        $password = $row['password'];
        $phone = $row['phone'];
        $email = $row['email'];
        $cardnumber = $row['cardnumber'];
        $status = $row['status'];
        $county = $row['county'];
    }else{
        print "No rows matched the query. try again click<a href='viewUpdateDelete.php'> here</a> to go back";
    }
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}
include 'updateDetails.html';
?>