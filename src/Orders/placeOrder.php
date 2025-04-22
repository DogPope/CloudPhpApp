<?php
session_start();
require '../../../vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient; 
use Aws\Exception\AwsException;

$client = new SecretsManagerClient([
    'version' => 'latest',
    'region' => 'eu-west-1'
]);

$result = $client->getSecretValue([
    'SecretId' => $_ENV["SECRET_NAME"],
]);

$myJSON = json_decode($result['SecretString']);
// Does this work? I think this might actually work?
define('DB_SERVER', $_ENV["DB_ENDPOINT"]);
define('DB_USERNAME', $myJSON->username);
define('DB_PASSWORD', $myJSON->password);
define('DB_DATABASE', $myJSON->dbname); // FIXED THIS LINE
$dsn = "mysql:host=" . $myJSON->host .
       ";port=" . $myJSON->port .
       ";dbname=" . $myJSON->dbname .
       ";charset=utf8"; // optional but recommended

$total = 0;

// Automatically boots the user back to login. NOTE: THERE IS NO DELAY IN REDIRECTION. It is instantaneous.
if(!isset($_SESSION['isLoggedIn'])){
    header("Location: ../Login/login.php");
    exit();
}

// Removes Items from cart on individual basis. Works, so don't remove it.
if(isset($_GET['action']) && $_GET['action'] == "remove" && isset($_GET['game_id'])) {
    foreach($_SESSION['cart'] as $key => $value) {
        if($value['game_id'] == $_GET['game_id']) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            break;
        }
    }
    header("Location: placeOrder.php"); // Redirect to refresh the cart
    exit();
}

// Adds item to cart.
if(isset($_POST['addToCart'])){
    $game_id = $_GET['game_id'];
    $title = $_POST['title'];
    $saleprice = $_POST['saleprice'];
    $quantity = (int)$_POST['quantity'];
    $amount = (int)$_POST['amount'];
    
    // Validate amount
    if($amount <= 0){
        $_SESSION['cart_error'] = "Please select a valid quantity.";
        header("Location: placeOrder.php");
        exit();
    }
    
    // Check stock availability
    if($amount > $quantity){
        $_SESSION['cart_error'] = "You cannot order more than we have in stock!";
        header("Location: placeOrder.php");
        exit();
    }
    
    // Initialize cart if not set
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();
    }
    
    // Check if item already in cart
    $item_exists = false;
    foreach($_SESSION['cart'] as $key => $item){
        if($item['game_id'] == $game_id){
            // Update quantity if total doesn't exceed stock
            $new_amount = $item['amount'] + $amount;
            if($new_amount <= $quantity){
                $_SESSION['cart'][$key]['amount'] = $new_amount;
            } else {
                $_SESSION['cart_error'] = "Cannot add more. Would exceed available stock.";
            }
            $item_exists = true;
            break;
        }
    }
    
    // Add new item if not already in cart
    if(!$item_exists){
        $session_array = array(
            'game_id' => $game_id,
            'title' => $title,
            'saleprice' => $saleprice,
            'quantity' => $quantity,
            'amount' => $amount
        );
        $_SESSION['cart'][] = $session_array;
    }
    
    // Redirect to avoid resubmission
    header("Location: placeOrder.php");
    exit();
}

// Contol block for displaying messages to the user.
if(isset($_SESSION['cart_error'])){
    echo '<div style="color: red; text-align: center; margin: 10px 0;">' . $_SESSION['cart_error'] . '</div>';
    unset($_SESSION['cart_error']);
}
// Display success message if order is placed successfully.
if(isset($_SESSION['order_success'])){
    echo '<div style="color: green; text-align: center; margin: 10px 0;">' . $_SESSION['order_success'] . '</div>';
    unset($_SESSION['order_success']);
}
include("../../public/html/header.html");

try{
    $pdo = new PDO($dsn, $myJSON->username, $myJSON->password);
    echo "Connection was Successful";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT game_id, title, developer, saleprice, quantity FROM Games WHERE quantity > 0 AND Status="R"';
    $result = $pdo->query($sql);

    $getMaxId = 'SELECT MAX(order_id) + 1 FROM Orders';
    $max = $pdo->query($getMaxId);

    $maxOrderNumber = $max->fetch();
    echo "Next order ID: ".$maxOrderNumber['MAX(order_id) + 1']."<br>";
    echo "Hello there, ".$_SESSION['username']."!";

    echo "<section style='text-align:center;'>";

    echo "<br /><b>Product Display</b><br><br>";
    echo "<table border=2 style='border-collapse:collapse;'>";
    echo "<tr><th>Game Id</th><th>Title:</th><th>Developer</th><th>Sale Price</th><th>Quantity</th><th>Add To Cart:</th></tr>";

    while ($row = $result->fetch()){
        $counter = 1;
        echo '<tr><td>' . $row['game_id'] . '</td><td>'. $row['title'] . '</td><td>'.$row['developer'].'</td><td>'.$row['saleprice'].'</td><td>'.$row['quantity'].' Left in Stock!</td>';
            echo 
            /* This table contains 4 hidden inputs within a hidden form corresponding to the relevant array values.
            It sends these values each time the shopping cart button is pressed. This is unwanted behaviour.*/
            '<td>' .$row['game_id'] . '
                <form method="post" action="placeOrder.php?game_id='.$row['game_id'].'">
                    <input type="hidden" name="game_id" value='.$row['game_id'].'>
                    <input type="hidden" name="title" value='.$row['title'].'>
                    <input type="hidden" name="saleprice" value='.number_format($row['saleprice'], 2).'>
                    <input type="hidden" name="quantity" value='.number_format($row['quantity']).'>
                    <input type="number" name="amount" value="1">
                    <img class="productimage" src="/public/images/img1.jpg">
                    <input type="submit" id="cartButton" name="addToCart" value="Add To Cart">
                </form>
            </td>';
        echo "</tr>";
        // Out of curiosity, I may try another increment operator here. $a++
        $counter = $counter + 1;
    }
    echo '</table><br><br>';

    if(!empty($_SESSION['cart'])){
        echo "<table border=2 style='border-collapse:collapse;'>";
        echo "<tr><th>Game ID</th><th>Title</th><th>Price Per Unit</th><th>Quantity Remaining</th><th>Amount Selected</th><th>Subtotal</th><th>Action</th></tr>";
        
        $total = 0;
        // Should remove all items from cart.
        if(isset($_GET['action']) && $_GET['action'] == "clearall"){
            unset($_SESSION['cart']);
            //header("Location: placeOrder.php");
            exit();
        }
        // Displays table of Cart items, with a button to remove each item.
        foreach($_SESSION['cart'] as $key => $value){
            // Convert price to float
            $price = (float)str_replace(',', '', $value['saleprice']);
            $amount = (int)$value['amount'];
            $itemTotal = $price * $amount;
            $total += $itemTotal;
            
            echo "<tr>";
                echo "<td>".$value['game_id']."</td>";
                echo "<td>".$value['title']."</td>";
                echo "<td>$".number_format($price, 2)."</td>";
                echo "<td>".$value['quantity']."</td>";
                echo "<td>".$amount."</td>";
                echo "<td>$".number_format($itemTotal, 2)."</td>";
                echo "<td><a href='placeOrder.php?action=remove&game_id=".$value['game_id']."'><button>Remove From Cart</button></a></td>";
            echo "</tr>";
        }
        // Display total row outside the loop
        echo "<tr>";
            echo "<td colspan='5'><strong>Total</strong></td>";
                echo "<td>$".number_format($total, 2)."</td>";
                echo "<td><a href='placeOrder.php?action=clearall'><button>Clear all from Cart</button></a></td>";
            echo "</tr>";
        echo "</table>";
    }
    // Display Place order button.
    echo "<a href='placeOrder.php?action=placeorder'>
                <button>Place Order</button>
          </a>";

    if(!empty($_SESSION['cart'])){
        // This block of code handles database interactions including INSERT and some housekeeping for Games Table.
        if(isset($_GET['action']) == "placeorder"){
            $insert = 'INSERT INTO Orders (Order_Date, cost, Cust_Id, Status) VALUES (Curdate(), :total, :cust_id, "P")';
            $stmt = $pdo->prepare($insert);
            $stmt->bindValue(':total', $total);
            $stmt->bindValue(':cust_id', $_SESSION['cust_id']);
            $stmt->execute();

            $itemInsert = 'INSERT INTO Order_Items (Order_Id, Game_Id) VALUES (:Order_Id, :Game_Id)';
            $nextstmt = $pdo->prepare($itemInsert);
            $nextstmt->bindValue(':Order_Id', $maxOrderNumber['MAX(order_id) + 1']);
            foreach($_SESSION['cart'] as $key => $value){
                // Execute Insert into OrderItems for each Game ID in the array.
                $nextstmt->bindValue(':Game_Id', $value['game_id']);
                    
                $nextstmt->execute();

                $reduceQty = 'UPDATE Games SET Quantity = Quantity-:amount WHERE Game_Id=:Game_Id';
                $reduceStmt = $pdo->prepare($reduceQty);
                $reduceStmt->bindValue(':Game_Id', $value['game_id']);
                $reduceStmt->bindValue(':amount', $value['amount']);

                $reduceStmt->execute();
            }
            unset($_SESSION['cart']);
            exit();
        }
    }
}catch(PDOException $e){
    $output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}

include("../../public/html/footer.html");
echo "</section>";
echo "</body></html>";

?>