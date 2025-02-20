<?php
try {
    include("../../../public/html/header.html"); 
$pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', ''); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql =  'update games set title = :gtitle,
            developer = :gdeveloper,
            publisher = :gpublisher,
            genre = :ggenre,
            description = :gdescription,
            buyprice = :gbuyprice,
            saleprice = :gsaleprice,
            quantity = :gquantity,
            status = :gstatus
            WHERE game_id = :gid';
$result = $pdo->prepare($sql);
$result->bindValue(':gid', $_POST['ud_id']);

if($_POST['ud_title'] == "" || strlen($_POST['ud_title'] > 20)){
    echo "You must enter a valid title to continue!<br>";
    echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
    return;
}
else{
    $result->bindValue(':gtitle', $_POST['ud_title']);
}

if($_POST['ud_developer'] == ""){
    echo "You must enter a valid Developer to continue!<br>";
    echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
    return;
}
else{
    $result->bindValue(':gdeveloper', $_POST['ud_developer']);
}

if($_POST['ud_publisher'] == ""){
    echo "You must enter a valid Publisher to continue!<br>";
    echo "Click <a href='viewUpdateDelete.php'>Here</a> To return!";
    return;
}
else{
    $result->bindValue(':gpublisher', $_POST['ud_publisher']);
}

// These two fields are nullable, so validation not necessary.
$result->bindValue(':ggenre', $_POST['ud_genre']);

$result->bindValue(':gdescription', $_POST['ud_description']);

$buyPrice = (float)$_POST['ud_buyprice'];
if($buyPrice > 1000000 || $buyPrice < 0){
    echo 'You need to enter a valid buying price to continue!';
    echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
    return;
}
else{
    $result->bindValue(':gbuyprice', $_POST['ud_buyprice']);
}

$salePrice = (float)$_POST['ud_saleprice'];
if($salePrice > 1000000 || $salePrice < 0){
    echo 'You need to enter a valid sale price to continue!';
    echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
    return;
}
else{
    $result->bindValue(':gsaleprice', $_POST['ud_saleprice']);
}

$quantity = (int)$_POST['ud_quantity'];
if($quantity >= 0 || $quantity >= 0){
    $result->bindValue(':gquantity', $_POST['ud_quantity']);
}
else{
    echo 'You need to enter a valid quantity of games to continue!';
    echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
    return;
}

if($_POST['ud_status'] == "Registered" || $_POST['ud_status'] == "Deregistered"){
    $result->bindValue(':gstatus', $_POST['ud_status']);
}
else{
    echo 'You need to enter "Registered" or "Deregistered" as a status to continue!';
    echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
    return;
}

$result->execute();
//For most databases, PDOStatement::rowCount() does not return the number of rows affected by a SELECT statement.
     
$count = $result->rowCount();
if ($count > 0)
{
echo "You just updated Game no: " . $_POST['ud_id'] ."  click<a href='viewUpdateDelete.php'> here</a> to go back ";
}
else
{
echo "nothing updated click<a href='viewUpdateDelete.php'> here</a> to go back";
}
}
 
catch (PDOException $e) { 

$output = 'Unable to process query sorry : ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 

}
?>