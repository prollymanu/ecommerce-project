<?php

include('connection.php');


$stmt = $conn->prepare("SELECT * FROM products LIMIT 4");
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();

$featured_products = $stmt->get_result();
if (!$featured_products) {
    die("Query execution failed: " . $stmt->error);
}








?>