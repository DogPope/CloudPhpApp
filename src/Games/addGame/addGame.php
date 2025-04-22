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
var_dump($dsn, $myJSON->username, $myJSON->password);
include '../../../public/html/header.html';
if (isset($_POST['submitdetails'])) {
    try {
        $title = $_POST['title'];
        $developer = $_POST['developer'];
        $genre = $_POST['genre'];
        $saleprice = $_POST['saleprice'];
        $quantity = $_POST['quantity'];
        if ($title == '' or $developer == ''){
            echo("You did not complete the insert form correctly <br> ");
        }else{
            $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
            echo "Connection was Successful";
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO Games (title, developer, genre, saleprice, quantity, status) 
            VALUES (:title, :developer, :genre, :saleprice, :quantity,'R')";
                
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':developer', $developer);
            $stmt->bindValue(':genre', $genre);
            $stmt->bindValue(':saleprice', $saleprice);
            $stmt->bindValue(':quantity', $quantity);
            $stmt->execute();
            header('location: addGame.php');
        }
    }
    catch(PDOException $e){
        error_log("Connection failed: " . $e->getMessage());
        die("Database connection failed. Please try again later.");
    }
}
include("addGame.html");
include('../../../public/html/footer.html');
?>