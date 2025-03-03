<?php
include '../../../public/html/header.html';
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
            for($i =0; $i < strlen($email); $i++){
                if($email[$i] == '@');
                    $numOfAtSymbols++;
                if($email[$i] == '.');
                    $domain++;
            }
            for($i = 0; $i < strlen($cardnumber); $i++){
                if(!is_numeric($cardnumber[$i]))
                    echo '<script language="javascript">';
                    echo 'alert("You have entered an invalid credit card number!")';
                    echo '</script>';
            }
            if($numOfAtSymbols < 1){
                echo 'alert("The email address you have entered is wrong!")';
                echo '<script language="javascript">';
                echo '</script>';
            }
            if($domain < 1 && $domain > 3){
                echo '<script language="javascript">';
                echo 'alert("You have entered an invalid email address!")';
                echo '</script>';
            }
            if(empty($password)){
                echo '<script language="javascript">';
                echo 'alert("You have entered an invalid password!")';
                echo '</script>';
            }
            if(empty($town)){
                echo '<script language="javascript">';
                echo 'alert("You must enter what approximate town you live in!")';
                echo '</script>';
            }
            if(empty($username)){
                    echo '<script language="javascript">';
                    echo 'alert("You have entered an invalid username!")';
                    echo '</script>';
            }
            else{
                $pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', '');
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
        }
        catch (PDOException $e) {
            $title = 'An error has occurred';
            $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        }
    }
include("addCustomer.html");
?>