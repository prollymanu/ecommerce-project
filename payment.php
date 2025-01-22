<?php
session_start();
include('server/connection.php');

// Initialize error variable
$error_message = "";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user information and payment details
$user_id = $_SESSION['user_id'];
$total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;

// Validate total amount
if ($total <= 0) {
    die("Invalid payment amount. Please try again.");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['phoneNumber'])) {
        $error_message = "M-Pesa number is required.";
    } else {
        // Get and sanitize the phone number
        $phoneNumber = preg_replace('/^0/', '254', $_POST['phoneNumber']); // Replace leading 0 with 254
        $phoneNumber = filter_var($phoneNumber, FILTER_SANITIZE_NUMBER_INT);
        $_SESSION['phone_number'] = $phoneNumber; // Save phone number in session

        // M-Pesa API credentials
        $businessShortCode = "174379"; // Replace with your Paybill/Till number
        $passKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919"; // Replace with your Passkey
        $consumerKey = "ajyUgKRR3ox2OmCATGxIVOnhvo8IdoqnDyhhZYyP6GPaegG6"; // Replace with your Consumer Key
        $consumerSecret = "2xAWYkY9jaYv4h5UkhkAHlWyJA2b3pmrwROANHt5O7Du95zJuUiJ2BhlBDdpCJdL"; // Replace with your Consumer Secret
        $callbackUrl = "https://34b6-2c0f-fe38-2403-12fc-892a-91f1-6917-9624.ngrok-free.app/8000/callback.php"; // Replace with your actual callback URL

        // Generate Access Token
        $accessTokenUrl = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $credentials = base64_encode("$consumerKey:$consumerSecret");

        $curl = curl_init($accessTokenUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            file_put_contents('Mpesastkresponse.json', "Access Token Error: " . curl_error($curl) . "\n", FILE_APPEND);
            die("Failed to connect to M-Pesa API. Please try again later.");
        }
        curl_close($curl);

        $accessTokenData = json_decode($response, true);
        if (!isset($accessTokenData['access_token'])) {
            $error_message = "Failed to generate access token. Please try again later.";
        } else {
            $accessToken = $accessTokenData['access_token'];

            // Initiate STK Push
            $stkPushUrl = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
            $timestamp = date('YmdHis');
            $password = base64_encode($businessShortCode . $passKey . $timestamp);

            $stkPushRequest = [
                "BusinessShortCode" => $businessShortCode,
                "Password" => $password,
                "Timestamp" => $timestamp,
                "TransactionType" => "CustomerPayBillOnline",
                "Amount" => $total,
                "PartyA" => $phoneNumber,
                "PartyB" => $businessShortCode,
                "PhoneNumber" => $phoneNumber,
                "CallBackURL" => $callbackUrl,
                "AccountReference" => "Order Payment",
                "TransactionDesc" => "Payment for Order"
            ];

            $curl = curl_init($stkPushUrl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json"
            ]);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($stkPushRequest));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                file_put_contents('Mpesastkresponse.json', "STK Push Error: " . curl_error($curl) . "\n", FILE_APPEND);
                die("Failed to connect to M-Pesa API. Please try again later.");
            }
            curl_close($curl);

            $stkPushResponse = json_decode($response, true);
            file_put_contents('Mpesastkresponse.json', "STK Push Response: " . json_encode($stkPushResponse, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

            if (!isset($stkPushResponse['ResponseCode']) || $stkPushResponse['ResponseCode'] != "0") {
                $error_message = "Failed to initiate payment. Please try again.";
            } else {
                // Save transaction details in the database with "pending" status
                $transaction_id = uniqid('MPESA');
                $_SESSION['transaction_id'] = $transaction_id;
                $_SESSION['order_id'] = $_SESSION['order_id'] ?? null;

                $paymentInsertQuery = "
                    INSERT INTO payments (transaction_id, order_id, user_id, payment_amount, phone_number, payment_status, payment_date)
                    VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
                $stmt = $conn->prepare($paymentInsertQuery);
                $stmt->bind_param("siids", $transaction_id, $_SESSION['order_id'], $user_id, $total, $phoneNumber);

                if ($stmt->execute()) {
                    // Redirect to a waiting page (or confirmation page)
                    header("Location: server/complete_payment.php");
                    exit;
                } else {
                    $error_message = "Failed to record payment. Please try again.";
                }
            }
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .payment-container {
            max-width: 600px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #0a74da;
            border: none;
        }
        .btn-primary:hover {
            background-color: #084b9b;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <h1 class="text-center">Complete Your Payment</h1>
            <p class="text-center text-muted">Total Amount: <strong>Kes <?php echo number_format($total, 2); ?></strong></p>

            <?php if (!empty($error_message)): ?>
                <p class="error-message text-center"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="payment.php" method="POST">
                <div class="mb-3">
                    <label for="phoneNumber" class="form-label">M-Pesa Phone Number</label>
                    <input type="text" class="form-control" name="phoneNumber" id="phoneNumber" placeholder="eg.. 0700000000" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Pay with M-Pesa</button>
            </form>
        </div>
    </div>
</body>
</html>




