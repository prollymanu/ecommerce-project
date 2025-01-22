<?php
// Retrieve the JSON response from Safaricom
$callbackData = file_get_contents('php://input');

// Decode the JSON response
$data = json_decode($callbackData, true);

// Log the incoming callback data for debugging
file_put_contents('mpesa_callback.log', $callbackData . "\n", FILE_APPEND);

// Check if the callback contains the expected data
if (isset($data['Body']['stkCallback'])) {
    $callback = $data['Body']['stkCallback'];

    // Extract details from the callback
    $resultCode = $callback['ResultCode'];
    $resultDesc = $callback['ResultDesc'];
    $transactionId = null;
    $amount = null;
    $phoneNumber = null;

    // Only process the payment if it was successful (ResultCode 0)
    if ($resultCode == 0) {
        // Extract payment details from CallbackMetadata
        foreach ($callback['CallbackMetadata']['Item'] as $item) {
            if ($item['Name'] == 'MpesaReceiptNumber') {
                $transactionId = $item['Value'];
            } elseif ($item['Name'] == 'Amount') {
                $amount = $item['Value'];
            } elseif ($item['Name'] == 'PhoneNumber') {
                $phoneNumber = $item['Value'];
            }
        }

        // Ensure all necessary details are present
        if ($transactionId && $amount && $phoneNumber) {
            include('server/connection.php');

            // Update the payments table
            $updatePaymentQuery = "
                UPDATE payments 
                SET payment_status = 'success', 
                    transaction_id = ?, 
                    payment_amount = ?, 
                    payment_date = NOW()
                WHERE phone_number = ? 
                AND payment_status = 'pending'";
            $stmt = $conn->prepare($updatePaymentQuery);
            $stmt->bind_param("sds", $transactionId, $amount, $phoneNumber);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                // Update order status if payment was successful
                $selectOrderQuery = "SELECT order_id FROM payments WHERE transaction_id = ?";
                $stmt = $conn->prepare($selectOrderQuery);
                $stmt->bind_param("s", $transactionId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $order_id = $row['order_id'] ?? null;

                if ($order_id) {
                    $updateOrderQuery = "UPDATE orders SET order_status = 'paid' WHERE order_id = ?";
                    $stmt = $conn->prepare($updateOrderQuery);
                    $stmt->bind_param("i", $order_id);
                    $stmt->execute();
                }

                // Log success
                file_put_contents('Mpesastkresponse.json', "Payment successful: $transactionId\n", FILE_APPEND);
            } else {
                // Log failure to update the database
                file_put_contents('Mpesastkresponse.json', "Database update failed for $transactionId\n", FILE_APPEND);
            }
        } else {
            // Log missing details
            file_put_contents('Mpesastkresponse.json', "Missing required callback details\n", FILE_APPEND);
        }
    } else {
        // Log failed payment attempt
        file_put_contents('Mpesastkresponse.json', "Payment failed: $resultDesc\n", FILE_APPEND);
    }
} else {
    // Log unexpected callback structure
    file_put_contents('Mpesastkresponse.json', "Unexpected callback data structure\n", FILE_APPEND);
}
?>

