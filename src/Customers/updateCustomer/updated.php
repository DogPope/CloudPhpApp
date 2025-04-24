<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
try{
    include("../../../public/html/header.html");
    $db = Database::getInstance();
    $sql =  'update customers set username = :cusername,
                town = :ctown, eircode = :ceircode, password = :cpassword,
                phone = :cphone, email = :cemail, cardnumber = :ccardnumber,
                status = :cstatus, county = :ccounty WHERE cust_id = :cid';
    $result = $db->prepare($sql);
    $result->bindValue(':cid', $_POST['ud_id']);

    if($_POST['ud_username'] == ""){
        echo "You must enter a valid username to continue!<br>";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }
    $result->bindValue(':cusername', $_POST['ud_username']);

    if($_POST['ud_town'] == ""){
        echo "You must enter a valid town to continue!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }
    $result->bindValue(':ctown', $_POST['ud_town']);

    if($_POST['ud_eircode'] == ""){
        echo "You must enter a valid EIR Code to continue!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }
    $result->bindValue(':ceircode', $_POST['ud_eircode']);

    $password = $_POST['ud_password'];
    if(strlen($password) < 8){
        echo "You must enter a valid password to continue!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }
    $result->bindValue(':cpassword', $_POST['ud_password']);

    $phone = $_POST['ud_phone'];
    $phoneNumber = $phone.trim(" ");
    if(!is_numeric($phoneNumber)){
        echo "A phone number must be numeric and have 10 digits!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }
    $result->bindValue(':cphone', $_POST['ud_phone']);

    $email = $_POST['ud_email'];
    if(str_contains($email,'@') && str_contains($email,'.')){
        $result->bindValue(':cemail', $_POST['ud_email']);
    }
    else{
        echo "An email must have a domain level and a address!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }

    $card = $_POST['ud_cardnumber'];
    $cardNumber = $card.trim(' ');
    if(is_numeric($cardNumber)){
        $result->bindValue(':ccardnumber', $_POST['ud_cardnumber']);
    }
    else{
        echo "A card number must be numeric!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }

    if($_POST['ud_status'] == "R" || $_POST['ud_status'] == "D"){
        $result->bindValue(':cstatus', $_POST['ud_status']);
    }
    else{
        echo "A status must be either `R` Registered or `D` (Deregistered)!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }
    $result->bindValue(':ccounty', $_POST['ud_county']);

    $result->execute();
        
    $count = $result->rowCount();
    if ($count > 0){
    echo "You just updated Customer no: " . $_POST['ud_id'] ."  click<a href='viewUpdateDelete.php'> here</a> to go back ";
    }else{
    echo "Nothing has been updated. Click <a href='viewUpdateDelete.php'> here</a> to go back";
    }
}catch(PDOException $e){
    $output = 'Unable to process query sorry : ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
    echo $output;
}
?>