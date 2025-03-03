<?php
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
            $pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', '');
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
        $title = 'An error has occurred';
        $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }
}
include("addGame.html");
include('../../../public/html/footer.html');
?>