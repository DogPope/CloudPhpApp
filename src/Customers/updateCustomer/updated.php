<?php
try { 
    include("../../../public/html/header.html");
$pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', ''); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$cust_id = $_POST['ud_id'];

$sql =  'update customers set forename = :cforename,
            surname = :csurname,
            town = :ctown,
            eircode = :ceircode,
            password = :cpassword,
            phone = :cphone,
            email = :cemail,
            cardnumber = :ccardnumber,
            status = :cstatus,
            county = :ccounty
            WHERE cust_id = :cid';
$result = $pdo->prepare($sql);
$result->bindValue(':cid', $_POST['ud_id']);

// All validation here takes the format: Validate, then assign value, then linebreak.

if($_POST['ud_forename'] == ""){
    echo "You must enter a valid forename to continue!<br>";
    echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
    return;
}
$result->bindValue(':cforename', $_POST['ud_forename']);

if($_POST['ud_surname'] == ""){
    echo "You must enter a valid surname to continue!<br>";
    echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
    return;
}
$result->bindValue(':csurname', $_POST['ud_surname']);

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

// Checks whether a password has two lower case letters, upper case letters and symbols for verification.
// This entire piece of code doesn't work for some reason.
/*$specialChars = "@%!#$%^&*()?/>.<,:\|}]{[_~`=-+";
$upperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$lowerCase = "abcdefghijklmnopqrstuvwxyz";
$specialCounter = 0;
$upperCaseCount = 0;
$lowerCaseCount = 0;
    for($index = 0; $index < strlen($specialChars); $index++){
        if(str_contains($password, $specialChars[$index]))
            $specialCounter++;
    }

    for($index = 0; $index < strlen($upperCase); $index++){
        if(str_contains($password, $upperCase[$index]))
            $upperCaseCount++;
    }

    for($index = 0; $index < strlen($lowerCase); $index++){
        if(str_contains($password, $lowerCase[$index]))
            $lowerCaseCount++;
    }

    if($upperCaseCount <= 2 || $lowerCaseCount <= 2){
        echo "We require a password with at least 2 of each lower case letter, upper case letter and symbols. We take your security very seriously you know!";
        echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
        return;
    }*/
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

if($_POST['ud_status'] == "Registered" || $_POST['ud_status'] == "Deregistered"){
    $result->bindValue(':cstatus', $_POST['ud_status']);
}
else{
    echo "A status must be either Registered or Deregistered!";
    echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
    return;
}
$result->bindValue(':ccounty', $_POST['ud_county']);

$result->execute();
//For most databases, PDOStatement::rowCount() does not return the number of rows affected by a SELECT statement.
     
$count = $result->rowCount();
if ($count > 0)
{
echo "You just updated customer no: " . $_POST['ud_id'] ."  click<a href='viewUpdateDelete.php'> here</a> to go back ";
}
else
{
echo "nothing updated <a href='viewUpdateDelete.php'> here</a> to go back";
}
}
 
catch (PDOException $e) { 

$output = 'Unable to process query sorry : ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 

}
?>