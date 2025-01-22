<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User not logged in. Please log in to proceed.'); window.location.href='login.php';</script>";
    exit;
}

// Check if the necessary session variables are set
if (
    !isset($_SESSION['transaction_id']) || 
    !isset($_SESSION['order_id']) || 
    !isset($_SESSION['total']) || 
    !isset($_SESSION['phone_number'])
) {
    echo "<script>alert('Missing payment details. Please try again.'); window.location.href='cart.php';</script>";
    exit;
}

// Retrieve session variables
$user_id = $_SESSION['user_id'];
$order_id = $_SESSION['order_id'];
$transaction_id = $_SESSION['transaction_id']; // This should now be the unique code from M-Pesa
$total = $_SESSION['total']; // Payment amount
$phone_number = $_SESSION['phone_number']; // Phone number used for the payment

// Start a database transaction
$conn->begin_transaction();

try {
    
    // Insert payment details into the payments table
    $insertPaymentQuery = "INSERT INTO payments (transaction_id, order_id, user_id, payment_amount, payment_date, phone_number) 
                           VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($insertPaymentQuery);
    $stmt->bind_param("siids", $transaction_id, $order_id, $user_id, $total, $phone_number);
    if (!$stmt->execute()) {
        throw new Exception("Failed to record payment.");
    }

    // Update order status to "paid"
    $updateOrderQuery = "UPDATE orders SET order_status = 'paid' WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateOrderQuery);
    $stmt->bind_param("ii", $order_id, $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update order status.");
    }

    // Commit the transaction
    $conn->commit();

    // Redirect the user to their account or orders page
    header("location: ../account.php?payment_message=Payment successful! Thank you for shopping with us");
    exit;
} catch (Exception $e) {
    // Rollback the transaction on failure
    $conn->rollback();

    // Log the error
    file_put_contents('payment_error.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);

    // Notify the user
    echo "<script>alert('An error occurred: " . $e->getMessage() . "'); window.location.href='orders_details.php';</script>";
    exit;
}
?>





