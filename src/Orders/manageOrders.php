<?php
session_start();
require '../../vendor/autoload.php';
require '../../bootstrap.php';
use App\Core\Database;
include("../../public/html/header.html");

if(!isset($_SESSION['isLoggedIn'])){
    $_SESSION['login_message'] = 'You need to be logged in to place an order!';
    header("Location: ../Login/login.php");
    exit();

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
    }else{
        $session_array = array(
              'game_id' => $_GET['game_id'],
              'title' => $_POST['title'],
              'saleprice' => $_POST['saleprice'],
        );
        $_SESSION['order_items'][] = $session_array;
    }
}
try{
    $db = Database::getInstance();

    // When user loads page, Cust ID is taken from session and used to bring up a table of Orders associated with that ID.
    $OrderSQL = 'SELECT DISTINCT order_id, DATE_FORMAT(order_date, "%d %M %Y") as ord_date, cost, status FROM orders
                WHERE Status != "c" AND Status != "r" AND cust_id='.$_SESSION['cust_id'].'
                ORDER BY order_Id';
    
    // Reduces the cost from an order if individual item is removed.
    $updateOrder = 'Update orders SET cost=:cost';

    $result = $pdo->query($OrderSQL);

    echo "<section style='text-align:center;'>";
        echo '<table border=2 style="border-collapse:collapse;">
        <tr><th>Order ID</th><th>Order Date</th><th>Order Cost</th><th>Order Status</th><th>Display Order Details</th><th>Cancel Order</th></tr>';

        while($row = $result->fetch()){
            echo 
            '<tr>
                <th>'.$row['order_id'].'</th>
                <th>'.$row['ord_date'].'</th>
                <th>'.$row['cost'].'</th>
                <th>'.$row['status'].'</th>
                <th>
                    <form method="get" action="manageOrders.php">
                        <input type="submit" name="details" value='.$row['order_id'].'>Order Details</button>
                        <input type="hidden" value='.$row['order_id'].' name="order_id">
                    </form>
                </th>
                <th>
                    <form method="get" action="manageOrders.php">
                    <input type="submit" name="cancel" value='.$row['order_id'].'>Cancel Order</button>
                    <input type="hidden" value='.$row['order_id'].' name="order_id">
                    </form>
                </th>
            </tr>';
        }
        echo "</table>";
    echo "</section><br><br>";

    if(isset($_GET['details'])){
        // When View Order Details is clicked, Order ID is sent here to display individual Order Items to another table.
        $viewItems = 'SELECT order_items.order_id, games.title, games.saleprice, order_items.game_id
        FROM games 
        INNER JOIN order_items 
        ON order_items.game_Id = games.game_Id
        WHERE order_items.order_id=:order_id';

        $items = $db->prepare($viewItems);

        $items->bindValue(':order_id', $_GET['order_id']);
        $items->execute();

        echo '<table border=2 style="border-collapse:collapse;"><tr><th>Order ID</th><th>Title</th><th>Price</th><th>Game ID</th><th>Remove Game</th></tr>';
        while($row = $items->fetch()){
            echo '<tr>
                    <th>'.$row['order_Id'].'</th>
                    <th>'.$row['title'].'</th>
                    <th>'.$row['saleprice'].'</th>
                    <th>'.$row['game_id'].'</th>
                    <th>
                    <form method="get" action="manageOrders.php">
                        <input type="submit" name="remove" value='.$row['order_Id'].'>Remove Item</button>
                        <input type="hidden" value='.$row['game_id'].' name="game_id">
                        <input type="hidden" value='.$row['saleprice'].' name="saleprice">
                        <input type="hidden" value='.$row['order_Id'].' name="order_id">
                    </form>
                    </th>
                </tr>';
            echo "<br>";
        }
        echo "</table>";
    }
    if(isset($_GET['cancel'])){
        $cancel = "UPDATE orders SET status='c' WHERE order_id=:order_id";
        $stmt = $db->prepare($cancel);
        $stmt->bindValue(':order_id', $_GET['order_id']);

        $stmt->execute();
    }

    if(isset($_GET['remove'])){
        $removeItem = 'DELETE FROM order_items WHERE game_Id=:game_id AND order_id=:order_id';
        $stmtRemove = $db->prepare($removeItem);
        $stmtRemove->bindValue(':order_id', $_GET['order_id']);
        $stmtRemove->bindValue(':game_id', $_GET['game_id']);

        $stmtRemove->execute();

        $updateCost = 'UPDATE orders SET cost=cost-:saleprice WHERE order_id=:order_id';
        $stmtReduceCost = $pdo->prepare($updateCost);
        $stmtReduceCost->bindValue(':saleprice', $_GET['saleprice']);
        $stmtReduceCost->bindValue(':order_id', $_GET['order_id']);
        $stmtReduceCost->execute();
    }
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    echo $output;
}
include("../../public/html/footer.html");
echo "</section>";
echo "</body></html>";
?>