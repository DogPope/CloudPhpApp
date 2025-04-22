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
include('../../../public/html/header.html');
$numOfAtSymbols = 0;
$domain = 0;
if (isset($_POST['submitdetails'])) {
    try {
        $username = $_POST['username'];
        $town = $_POST['town'];
        $eircode = $_POST['eircode'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $cardnumber = $_POST['cardnumber'];
        $email = $_POST['email'];
        $county = $_POST['county'];
        if(strlen($town) < 3){
            echo '<script language="javascript">';
            echo    'alert("You must enter a valid town name!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        if(empty($username)){
            echo '<script language="javascript">';
            echo    'alert("You have entered an invalid username!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        if(!str_contains($eircode, ' ')){
            echo '<script language="javascript">';
            echo    'alert("You have entered an invalid Eircode! Please enter a space between the first 3 characters and the last 4 characters!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/", $password)){
            echo '<script language="javascript">';
            echo    'alert("You have entered an invalid password! Please enter a password with at least 8 characters, 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        if(!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$/", $email)){
            echo '<script language="javascript">';
            echo    'alert("You have entered an invalid Email Address! Please enter a valid email address!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        if(!preg_match("/^[0-9]{16}$/", $cardnumber)){
            echo '<script language="javascript">';
            echo    'alert("You have entered an invalid Card Number! Please enter a valid 16 digit card number!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        if(!preg_match("/^[0-9]{10}$/", $phone)){
            echo '<script language="javascript">';
            echo    'alert("You have entered an invalid Phone Number! Please enter a valid 10 digit phone number!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        // NOTE: This only checks for three letters, four numbers. E.g 'WER 2345'
        if(!preg_match("/^[A-Z]{3} [0-9]{4}$/", $eircode)){
            echo '<script language="javascript">';
            echo    'alert("You have entered an invalid Eircode! Please enter a valid Eircode!")';
            echo '</script>';
            header("Refresh:0");
            exit();
        }
        $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
        echo "Connection was Successful";
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO customers (username, town, eircode, password, phone, email, cardnumber, county, status) VALUES (:username, :town, :eircode, :password, :phone, :email, :cardnumber, :county, 'R')";  //CURDATE() - Method returns current time. Not useful here, but I'll comment it out for safekeeping!

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':town', $town);
        $stmt->bindValue(':eircode', $eircode);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':cardnumber', $cardnumber);
        $stmt->bindValue(':county', $county);
        $stmt->execute();
        header('location: addCustomer.php');
    }
    catch (PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        echo "Connection failed: " . $e->getMessage();
        die("Database connection failed. Please try again later.");
    }
}
include("addCustomer.html");
include('../../../public/html/footer.html');
?>