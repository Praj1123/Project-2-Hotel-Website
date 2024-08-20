<?php
// Include the Razorpay PHP SDK (you need to install it using Composer)
require 'vendor/autoload.php'; // Ensure you have installed Razorpay SDK via Composer

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$api = new Api('rzp_test_kh4YVbkeflxjPT', 'ecxB23YYhtSlxRLTeQozcON2');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'createOrder') {
        $amount = $_POST['amount'];

        try {
            $order = $api->order->create([
                'amount' => $amount * 100, // amount in the smallest currency unit
                'currency' => 'INR',
                'receipt' => 'receipt#1'
            ]);
            echo json_encode(['orderId' => $order['id']]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'verifyPayment') {
        $secret = 'YOUR_WEBHOOK_SECRET'; // Replace with your webhook secret
        $razorpayOrderId = $_POST['razorpay_order_id'];
        $razorpayPaymentId = $_POST['razorpay_payment_id'];
        $razorpaySignature = $_POST['razorpay_signature'];

        $body = $razorpayOrderId . "|" . $razorpayPaymentId;
        $expectedSignature = hash_hmac('sha256', $body, $secret);

        $response = ['signatureIsValid' => $expectedSignature === $razorpaySignature ? 'true' : 'false'];
        echo json_encode($response);
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Payment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="stylesheet.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
          }
          
          body {
            background: linear-gradient(to right, #917173, #2a3b36, #432c52);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
          }
          
          .loader {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
          }
          
          .loader::before {
            content: "";
            background: rgba(255, 255, 255, 0);
            backdrop-filter: blur(8px);
            position: absolute;
            width: 140px;
            height: 55px;
            z-index: 20;
            border-radius: 0 0 10px 10px;
            border: 1px solid rgba(255, 255, 255, 0.274);
            border-top: none;
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.082);
            animation: anim2 2s infinite;
          }
          
          .loader div {
            background: rgb(228, 228, 228);
            border-radius: 50%;
            width: 25px;
            height: 25px;
            z-index: -1;
            animation: anim 2s infinite linear;
            animation-delay: calc(-0.3s * var(--i));
            transform: translateY(5px);
            margin: 0.2em;
          }
          
          @keyframes anim {
            0%,
            100% {
              transform: translateY(5px);
            }
            50% {
              transform: translateY(-65px);
            }
          }
          
          @keyframes anim2 {
            0%,
            100% {
              transform: rotate(-10deg);
            }
            50% {
              transform: rotate(10deg);
            }
          }
          .center { 
  height: 200px;
  position: relative;
}

.center p {
  margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  color:white
}
          
    </style>
</head>
<body>
<div class="center" style='display:none' id='text'>
  <h1 style='color:white'>Wait...</h1>
</div>
<div class="loader" id='loader_div'>
        <div style="--i: 1"></div>
        <div style="--i: 2"></div>
        <div style="--i: 3"></div>
        <div style="--i: 4"></div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
setTimeout(() => {
    display()
}, 2000);
        function display() {
            $.ajax({
                url: "",
                method: "POST",
                timeout: 0,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                data: {
                    action: 'createOrder',
                    amount: 270
                }
            }).done(function (response) {
                response = JSON.parse(response);
                var orderId = response.orderId;
                var options = {
                    "key": "rzp_test_kh4YVbkeflxjPT", // Enter the Key ID generated from the Dashboard
                    "amount": 270, // Amount in currency subunits
                    "currency": "INR",
                    "image": "https://olive-capybara-568121.hostingersite.com/wp-content/uploads/2021/08/Green-and-Gold-Modern-Flat-Illustrated-Home-Hotel-and-Travel-Logo-3.png",
                    "order_id": orderId, // This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                    "handler": function (response) {
                        //payment success handling block
                        console.log(response.razorpay_payment_id);
                        console.log(response.razorpay_order_id);
                        console.log(response.razorpay_signature);
                        window.location.href = 'https://olive-capybara-568121.hostingersite.com/'
                        document.getElementById('loader_div').style.display='none'
                        document.getElementById('text').style.display='block'
                        $.ajax({
                            url: "",
                            method: "POST",
                            timeout: 0,
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            data: {
                                action: 'verifyPayment',
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            }
                        }).done(function (response) {
                            console.log(JSON.stringify(response));
                        });
                    },
                    "theme": {
                        "color": "#031B18"
                    }
                };

                var rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response) {
                    console.log(response.error.code);
                    console.log(response.error.description);
                    console.log(response.error.source);
                    console.log(response.error.step);
                    console.log(response.error.reason);
                    console.log(response.error.metadata.order_id);
                    console.log(response.error.metadata.payment_id);
                   //payment failur handling block
                });
                rzp1.open();
            });
        }

        function to_thank_you_page() {
                window.location.href = 'https://olive-capybara-568121.hostingersite.com/'
        }


        function sendSessionDataToServer() {
            // Retrieve data from session storage and local storage
            document.getElementById('loader').style.display='block'
            const name = sessionStorage.getItem('Name');
            const phoneNumber = sessionStorage.getItem('MobileNo');
            const amount = sessionStorage.getItem('Amount');
            const uniqueId = localStorage.getItem('dntidd');

            // Prepare data to send
            const data = {
                name: name,
                phoneNumber: phoneNumber,
                amount: amount,
                uniqueId: uniqueId
            };

            // Send data to PHP script
            fetch('process_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log('Success:', result);
                to_thank_you_page()
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
       // checkStatus()
        function checkStatus(){
            if(!localStorage.getItem('dntidd')){
                document.getElementById('alert').style.opacity='100%'
            }else{
                document.getElementById('donate').removeAttribute('disabled');
                console.log(localStorage.getItem('dntidd'))
            }
        }
        function saveRedirectUrl() {
    // Save the current URL or the URL where you want to redirect after sign-in
    sessionStorage.setItem('redirectURL', 'payment_gateway.php');
}
    </script>
</body>
</html>
