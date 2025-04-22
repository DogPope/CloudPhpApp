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
session_start();
include "../../public/html/header.html";
include("login.html");
$isLoggedIn = false;

if (isset($_POST['submitbutton'])){
    if (isset($_POST['email'])&& isset ($_POST['password'])){
        $email = $_POST['email'];
        $password = $_POST['password'];
    if(empty($email)){
        echo '<script language="javascript">';
        echo 'alert("You need to enter a valid email address to continue!")';
        echo '</script>';
        return;
    }
    if(empty($password)){
        echo '<script language="javascript">';
        echo 'alert("You have entered an incorrect password!")';
        echo '</script>';
        return;
    }
        try{
            $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
            echo "Connection was Successful";
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT cust_id, username, eircode, email, password FROM customers WHERE email=:email AND password=:password";
            $result = $pdo->prepare($sql);
            $result->bindValue(':email', $email);
            $result->bindValue(':password', $password);
            $result->execute();

            if($result->fetchColumn() > 0){
                $sql = "SELECT cust_id, username, eircode, email, password FROM customers WHERE email=:email AND password=:password";
                $result = $pdo->prepare($sql);
                $result->bindValue(':email', $email);
                $result->bindValue(':password', $password);
                $result->execute();

                $row = $result->fetch();
                $cust_id = $row['cust_id'];
                $username = $row['username'];
                $eircode = $row['eircode'];
                $password = $row['password'];
                $email = $row['email'];

                $isLoggedIn = true;

                $_SESSION['isLoggedIn'] = $isLoggedIn;
                $_SESSION['username'] = $username;
                $_SESSION['cust_id'] = $cust_id;
                $_SESSION['eircode'] = $eircode;
                $_SESSION['email'] = $email;

                echo "Hello ".$username;
            }else{
                echo '<script language="javascript">';
                echo 'alert("We do not have an account with those credentials. Please ensure everything is spelled correctly!")';
                echo '</script>';
                return;
            }
        }catch(PDOException $e){
            $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        }
    }
}
include("../../public/html/footer.html");
?>