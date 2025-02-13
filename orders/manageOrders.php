<?php
session_start();

if(!isset($_SESSION['isLoggedIn'])){
    echo "<script language='javascript'>";
    echo "alert('You need to be logged in to place an order!')";
    echo "</script>";
    echo "Click <a href='../login/login.php'>Here</a> to Log in!";

    if(isset($_SESSION['cart'])){
        unset($_SESSION['cart']);
    }

    if(isset($_SESSION['order_items'])){
        $session_array_id = array_column($_SESSION['order_items'], 'game_id');

        if(!in_array($_GET['game_id'], $session_array_id)){
              $session_array = array(
                    'game_id' => $_GET['game_id'],
                    'title' => $_POST['title'],
                    'saleprice' => $_POST['saleprice'],
              );
              $_SESSION['order_items'][] = $session_array;
        }
  }
  else{
        $session_array = array(
              'game_id' => $_GET['game_id'],
              'title' => $_POST['title'],
              'saleprice' => $_POST['saleprice'],
        );
        $_SESSION['order_items'][] = $session_array;
  }
}

include("../header.html");

try {
    $pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', ''); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // When user loads page, Cust ID is taken from session and used to bring up a table of Orders associated with that ID.
    $OrderSQL = 'SELECT DISTINCT Order_Id, DATE_FORMAT(Order_Date, "%d %M %Y") as ord_date, Cost, Status FROM Orders
                WHERE Status != "Cancelled" AND Status != "Returned" AND Cust_Id='.$_SESSION['cust_id'].'
                ORDER BY Order_Id';
    
    // Reduces the cost from an order if individual item is removed.
    $updateOrder = 'Update Orders SET Cost=:cost';

    $result = $pdo->query($OrderSQL);

    echo "<section style='text-align:center;'>";
        echo '<table border=2 style="border-collapse:collapse;">
        <tr><th>Order ID</th><th>Order Date</th><th>Order Cost</th><th>Order Status</th><th>Display Order Details</th><th>Cancel Order</th></tr>';

        while($row = $result->fetch()){
            echo 
            '<tr>
                <th>'.$row['Order_Id'].'</th>
                <th>'.$row['ord_date'].'</th>
                <th>'.$row['Cost'].'</th>
                <th>'.$row['Status'].'</th>
                <th>
                    <form method="get" action="manageOrders.php">
                        <input type="submit" name="details" value='.$row['Order_Id'].'>Order Details</button>
                        <input type="hidden" value='.$row['Order_Id'].' name="order_id">
                    </form>
                </th>
                <th>
                    <form method="get" action="manageOrders.php">
                    <input type="submit" name="cancel" value='.$row['Order_Id'].'>Cancel Order</button>
                    <input type="hidden" value='.$row['Order_Id'].' name="order_id">
                    </form>
                </th>
            </tr>';
        }
    echo "</section><br><br>";

    if(isset($_GET['details'])){
        // When View Order Details is clicked, Order ID is sent here to display individual Order Items to another table.
        $viewItems = 'SELECT Order_Items.Order_Id, Games.Title, Games.Saleprice, Order_Items.Game_Id
        FROM Games 
        INNER JOIN Order_Items 
        ON Order_Items.Game_Id = Games.Game_Id
        WHERE Order_Items.Order_Id=:order_id';

        $items = $pdo->prepare($viewItems);

        $items->bindValue(':order_id', $_GET['order_id']);
        $items->execute();

        echo '<table border=2 style="border-collapse:collapse;"><tr><th>Order ID</th><th>Title</th><th>Price</th><th>Game ID</th><th>Remove Game</th></tr>';
        while($row = $items->fetch()){
            echo '<tr>
                    <th>'.$row['Order_Id'].'</th>
                    <th>'.$row['Title'].'</th>
                    <th>'.$row['Saleprice'].'</th>
                    <th>'.$row['Game_Id'].'</th>
                    <th>
                    <form method="get" action="manageOrders.php">
                        <input type="submit" name="remove" value='.$row['Order_Id'].'>Remove Item</button>
                        <input type="hidden" value='.$row['Game_Id'].' name="game_id">
                        <input type="hidden" value='.$row['Saleprice'].' name="saleprice">
                        <input type="hidden" value='.$row['Order_Id'].' name="order_id">
                    </form>
                    </th>
                </tr>';
            echo "<br>";
        }
    }
    if(isset($_GET['cancel'])){
        $cancel = "UPDATE Orders SET Status='Cancelled' WHERE Order_Id=:order_id";
        $stmt = $pdo->prepare($cancel);
        $stmt->bindValue(':order_id', $_GET['order_id']);

        $stmt->execute();
    }

    if(isset($_GET['remove'])){
        $removeItem = 'DELETE FROM Order_Items WHERE Game_Id=:game_id AND Order_Id=:order_id';
        $stmtRemove = $pdo->prepare($removeItem);
        $stmtRemove->bindValue(':order_id', $_GET['order_id']);
        $stmtRemove->bindValue(':game_id', $_GET['game_id']);

        $stmtRemove->execute();

        $updateCost = 'UPDATE Orders SET Cost=Cost-:saleprice WHERE Order_ID=:order_id';
        $stmtReduceCost = $pdo->prepare($updateCost);
        $stmtReduceCost->bindValue(':saleprice', $_GET['saleprice']);
        $stmtReduceCost->bindValue(':order_id', $_GET['order_id']);
        $stmtReduceCost->execute();
    }

}
catch (PDOException $e) {
$output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}

echo "</section>";
echo "</body></html>";

?>