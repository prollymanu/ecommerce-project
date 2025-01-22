<?php

include('connection.php');


$stmt = $conn->prepare("SELECT * FROM products where product_category='new' LIMIT 4");
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();

$new_products = $stmt->get_result();
if (!$new_products) {
    die("Query execution failed: " . $stmt->error);
}