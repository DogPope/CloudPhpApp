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
define('DB_DATABASE', $myJSON->dbname);
$dsn="mysql:host=".$myJSON->host.";port=".$myJSON->port.";dbname=".$myJSON->dbname.";charset=utf8";
try{ 
    include("../../../public/html/header.html");
    $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
    echo "Connection was Successful";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT count(*) FROM customers where cust_id = :cid';
    $result = $pdo->prepare($sql);
    $result->bindValue(':cid', $_GET['cust_id']);
    $result->execute();
    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM customers where cust_id = :cid';
        $result = $pdo->prepare($sql);
        $result->bindValue(':cid', $_GET['cust_id']);
        $result->execute();
        
        while ($row = $result->fetch()){
            echo $row['username'] . ' ' . $row['town'] . ' Are you sure you want to delete?' . '<form action="deleteCustomer.php" method="post">
            <input type="hidden" name="cust_id" value="'.$row['cust_id'].'">
            <input type="submit" value="yes delete" name="delete">
            </form>';
        }
    }else{
        print("No rows matched the query.");
    }
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
}
?>