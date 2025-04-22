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
try{
    include("../../../public/html/header.html"); 
    $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
    echo "Connection was Successful"; 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql =  'update games set title = :gtitle, developer = :gdeveloper, genre = :ggenre,
                saleprice = :gsaleprice, quantity = :gquantity, status = :gstatus WHERE game_id = :gid';
    $result = $pdo->prepare($sql);
    $result->bindValue(':gid', $_POST['ud_id']);

    /*
    * Debug code block. Testing whether the SQL works or not.
    When all validation is removed, it still doesn't work.
    It's not the SQL either, as evidenced by the script below.
    update games set title = 'NotTitle', developer = 'Mr.Clean', genre = 'Mayhem', saleprice = 12.5, quantity = 6, status = 'R' WHERE game_id = 1;
    Note: This query DOES work.
    */

    // This is being triggered erroneously. Must Find cause. Title Validation triggered.
    if(strlen($_POST['ud_title']) < 1 || strlen($_POST['ud_title']) > 20){
        echo "You must enter a valid title to continue!<br>";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }else{
        $result->bindValue(':gtitle', $_POST['ud_title']);
    }

    if($_POST['ud_developer'] == ""){
        echo "You must enter a valid Developer to continue!<br>";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }else{
        $result->bindValue(':gdeveloper', $_POST['ud_developer']);
    }
// These two fields are nullable, so validation not necessary. Later on, the "Description" Field would get deleted for simplicities sake.
/* The previous comment was written in second year, so I would now go back and call that
one of the dumbest things ever written by a programmer lol.
In a past life, and so on and so forth.
*/
    $result->bindValue(':ggenre', $_POST['ud_genre']);

    $salePrice = (float)$_POST['ud_saleprice'];
    if($salePrice > 1000000 || $salePrice < 0){
        echo 'You need to enter a valid sale price to continue!';
        echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
        return;
    }else{
        $result->bindValue(':gsaleprice', $_POST['ud_saleprice']);
    }

    $quantity = (int)$_POST['ud_quantity'];
    if($quantity >= 0 || $quantity >= 0){
        $result->bindValue(':gquantity', $_POST['ud_quantity']);
    }else{
        echo 'You need to enter a valid quantity of games to continue!';
        echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
        return;
    }

    if($_POST['ud_status'] == "R" || $_POST['ud_status'] == "D"){
        $result->bindValue(':gstatus', $_POST['ud_status']);
    }else{
        echo 'You need to enter "Registered" or "Deregistered" as a status to continue!';
        echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
        return;
    }

    $result->execute();
//For most databases, PDOStatement::rowCount() does not return the number of rows affected by a SELECT statement.
     
    $count = $result->rowCount();
    if ($count > 0){
        echo "You just updated Game no: " . $_POST['ud_id'] ."  click<a href='viewUpdateDelete.php'> here</a> to go back ";
    }else{
        echo "nothing updated click<a href='viewUpdateDelete.php'> here</a> to go back";
    }
}catch(PDOException $e){
    $output = 'Unable to process query sorry : ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}
?>