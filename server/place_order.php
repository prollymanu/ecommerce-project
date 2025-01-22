<?php
session_start();
include('connection.php');

if (isset($_POST['place_order'])) {
    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('location: ../checkout.php?message=Please register/login to place an order');
        exit;
    }

    // Ensure the cart has items
    if (!isset($_SESSION['total']) || $_SESSION['total'] == 0) {
        die("Your cart is empty. Add items to the cart before placing an order.");
    }

    $order_cost = $_SESSION['total'];
    $order_status = "not paid";
    $user_id = $_SESSION['user_id'];
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $city = htmlspecialchars($_POST['city']);
    $idNumber = htmlspecialchars($_POST['idNumber']);
    $order_date = date('Y-m-d H:i:s');

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_idNumber, order_date)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isiisis', $order_cost, $order_status, $user_id, $phone, $city, $idNumber, $order_date);
    $stmt_status = $stmt->execute();

    if(!$stmt_status){
        header('location: index.php');
        exit;
    }

     // issuing new order and storing the order info in the database
    $order_id = $stmt->insert_id;

    // Insert into order_items table
    foreach ($_SESSION['cart'] as $key => $value) {
        $product_id = $value['product_id'];
        $product_name = $value['product_name'];
        $product_image = $value['product_image'];
        $product_price = $value['product_price'];
        $product_quantity = $value['product_quantity'];

        $stmt1 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param('iissiiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);
        $stmt1->execute();
    }

   // Clear the cart after placing the order
   unset($_SESSION['cart']);

   $_SESSION['order_id'] = $order_id;

    header('location: ../payment.php?order_status=Order placed successfully');
    exit();
}
?>
