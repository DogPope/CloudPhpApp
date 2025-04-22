<?php
require '../../../vendor/autoload.php';
require '../../../bootstrap.php';
use App\Core\Database;
try{
    include("../../../public/html/header.html"); 
    $db = Database::getInstance();
    $sql =  'update games set title = :gtitle, developer = :gdeveloper, genre = :ggenre,
                saleprice = :gsaleprice, quantity = :gquantity, status = :gstatus WHERE game_id = :gid';
    $result = $db->query($sql);
    $result->bindValue(':gid', $_POST['ud_id']);

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

    if($_POST['ud_status'] == "r" || $_POST['ud_status'] == "d"){
        $result->bindValue(':gstatus', $_POST['ud_status']);
    }else{
        echo 'You need to enter "Registered" or "Deregistered" as a status to continue!';
        echo "Click <a href='viewUpdateDelete.php'>Here </a> To go back!";
        return;
    }
    $result->execute();

    $count = $result->rowCount();
    if ($count > 0){
        echo "You just updated Game no: " . $_POST['ud_id'] ."  click<a href='viewUpdateDelete.php'> here</a> to go back ";
    }else{
        echo "nothing updated click<a href='viewUpdateDelete.php'> here</a> to go back";
    }
}catch(PDOException $e){
    $output = 'Unable to process query sorry : ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    echo $output;
}
?>