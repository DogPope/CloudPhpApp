<?php
require '../../../vendor/autoload.php';

use App\Core\Database;

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
        $db = Database::getInstance();
            
            $custId = $db->insert('customers', [
                'username' => $username,
                'town' => $town,
                'eircode' => $eircode,
                'password' => $password,
                'phone' => $phone,
                'cardnumber' => $cardnumber,
                'email' => $email,
                'county' => $county
            ]);
            
            if ($custId) {
                echo "Customer added successfully with the following Username: $username";
            } else {
                echo "Error adding Customer. Please try again.";
            }
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