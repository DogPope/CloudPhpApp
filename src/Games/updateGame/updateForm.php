<?php
include("/CloudPhpApp/public/html/header.html");
try{
    $pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql="SELECT count(*) FROM games WHERE game_id=:gid";
    $result = $pdo->prepare($sql);
    $result->bindValue(':gid', $_GET['game_id']);
    $result->execute();

    if($result->fetchColumn() > 0){
        $sql = 'SELECT * FROM games where game_id=:gid';
        $result = $pdo->prepare($sql);
        $result->bindValue(':gid', $_GET['game_id']);
        $result->execute();

        $row = $result->fetch();
        $game_id = $row['game_id'];
        $title = $row['title'];
        $developer = $row['developer'];
        $publisher = $row['publisher'];
        $genre = $row['genre'];
        $description = $row['description'];
        $buyprice = $row['buyprice'];
        $saleprice = $row['saleprice'];
        $quantity = $row['quantity'];
        $status = $row['status'];
    }else{
        print "No rows matched the query. try again click<a href='viewUpdateDelete.php'> here</a> to go back";
    }
}catch(PDOException $e) { 
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(); 
}
include("updateDetails.html");
?>