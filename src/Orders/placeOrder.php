<?php
session_start();

$total = 0;

if(!isset($_SESSION['isLoggedIn'])){
      echo "<script language='javascript'>";
      echo "alert('You need to be logged in to place an order!')";
      echo "</script>";
      echo "Click <a href='../login/login.php'>Here</a> to Log in!";
}

if(isset($_POST['addToCart'])){
      if($_POST['amount'] > $_POST['quantity']){
            echo "You cannot order more than we have in stock!";
            return;
      }

      if(isset($_SESSION['cart'])){
            $session_array_id = array_column($_SESSION['cart'], 'game_id');

            if(!in_array($_GET['game_id'], $session_array_id)){
                  $session_array = array(
                        'game_id' => $_GET['game_id'],
                        'title' => $_POST['title'],
                        'saleprice' => $_POST['saleprice'],
                        'quantity' => $_POST['quantity'],
                        'amount' => $_POST['amount']
                  );
                  $_SESSION['cart'][] = $session_array;
            }
      }
      else{
            $session_array = array(
                  'game_id' => $_GET['game_id'],
                  'title' => $_POST['title'],
                  'saleprice' => $_POST['saleprice'],
                  'quantity' => $_POST['quantity'],
                  'amount' => $_POST['amount']
            );
            $_SESSION['cart'][] = $session_array;
      }

}

include("../../public/html/header.html");

   try {
            $pdo = new PDO('mysql:host=localhost;dbname=shippingapp; charset=utf8', 'root', 'password'); 
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SELECT game_id, title, developer, saleprice, quantity FROM Games WHERE quantity > 0 AND Status="Registered"';
            $result = $pdo->query($sql);

            $getMaxId = 'SELECT MAX(order_id) + 1 FROM Orders';
            $max = $pdo->query($getMaxId);

            $maxOrderNumber = $max->fetch();
            echo "Next order ID: ".$maxOrderNumber['MAX(order_id) + 1'];

            echo "<section style='text-align:center;'>";

            echo "<br /><b>Product Display</b><br><br>";
            echo "<table border=2 style='border-collapse:collapse;'>";
            echo "<tr><th>Game Id</th><th>Title:</th><th>Developer</th><th>Sale Price</th><th>Quantity</th><th>Add To Cart:</th></tr>";

            while ($row = $result->fetch()) {
                  $counter = 1;
            echo '<tr><td>' . $row['game_id'] . '</td><td>'. $row['title'] . '</td><td>'.$row['developer'].'</td><td>'.$row['saleprice'].'</td><td>'.$row['quantity'].' Left in Stock!</td>';
                  echo 
                        /* This table contains 4 hidden inputs within a hidden form corresponding to the relevant array values.
                           It sends these values each time the shopping cart button is pressed.*/
                        '<td>' .$row['game_id'] . '
                              <form method="post" action="placeOrder.php?game_id='.$row['game_id'].'">
                                    <input type="hidden" name="game_id" value='.$row['game_id'].'>
                                    <input type="hidden" name="title" value='.$row['title'].'>
                                    <input type="hidden" name="saleprice" value='.number_format($row['saleprice'], 2).'>
                                    <input type="hidden" name="quantity" value='.number_format($row['quantity']).'>
                                    <input type="number" name="amount" value="1">
                                    <img class="productimage" src="../images/img'.$counter.'.jpg">
                                    <input type="submit" id="cartButton" name="addToCart" value="Add To Cart">
                              </form>
                        </td>';
            echo "</tr>";
            $counter = $counter + 1;
            }
echo '</table><br><br>';

      if(!empty($_SESSION['cart'])){
            echo "<table border=2 style='border-collapse:collapse;'><tr><th>Game ID</th><th>Title</th><th>Price Per Unit</th><th>Quantity Remaining</th><th>Amount Selected</th></tr>";
            $total=0;
            foreach($_SESSION['cart'] as $key => $value){
                  $output = "
                        <tr>
                              <td>".$value['game_id']."</td>
                              <td>".$value['title']."</td>
                              <td>".$value['saleprice']."</td>
                              <td>".$value['quantity']."</td>
                              <td>".$value['amount']."</td>
                              <td>".number_format($value['saleprice'] * $value['amount'], 2)."</td>
                              <td>
                                    <a href='placeOrder.php?action=remove&game_id=".$value['game_id']."'>
                                          <button>Remove From Cart</button>
                                    </a>
                              </td>
                        </tr>
                        <tr>
                        <th>Total</th>
                        <td>".$total = $total + (int)$value['amount'] * (float)$value['saleprice']."</td>
                        <td>
                              <a href='placeOrder.php?action=clearall'>
                                    <button>Clear all from Cart</button>
                              </a>
                        </td>
                        </tr>
                  </table>";
            }
            echo $output;
      }
            echo "<a href='placeOrder.php?action=placeorder'>
                        <button>Place Order</button>
                  </a>";

      if(!empty($_SESSION['cart'])){
            // This block of code handles database interactions including INSERT and some housekeeping for Games Table.
            if(isset($_GET['action']) == "placeorder"){
                  $insert = 'INSERT INTO Orders (Order_Date, cost, Cust_Id, Status) VALUES (Curdate(), :total, :cust_id, "Placed")';
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
      
                        // Prepare Statement for reducing the quantity of items remaining in the database.
                        $reduceQty = 'UPDATE Games SET Quantity = Quantity-:amount WHERE Game_Id=:Game_Id';
                        $reduceStmt = $pdo->prepare($reduceQty);
                        $reduceStmt->bindValue(':Game_Id', $value['game_id']);
                        $reduceStmt->bindValue(':amount', $value['amount']);
      
                        $reduceStmt->execute();
                  }
      
                  // Remove everything from shopping cart array after order is placed. Or it should but doesn't?
                  unset($_SESSION['cart']);
            }

            // Clears all from cart
            if(isset($_GET['action']) == "clearall"){
                  unset($_SESSION['cart']);
            }
      }

      }
catch (PDOException $e) {
$output = 'Unable to connect to the database server: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}
echo "</section>";
echo "</body></html>";

?>