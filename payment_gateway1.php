<?php
// Include the Razorpay PHP SDK (you need to install it using Composer)
require 'vendor/autoload.php'; // Ensure you have installed Razorpay SDK via Composer

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$api = new Api('rzp_test_pPwp0WB2oRjxXV', 'nLmWKh2YMTk29Ody8IiFTVWE');

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
    <title>Donation Form</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="stylesheet.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .btn-selected {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card p-4" style="border:2px solid #DA251C;">
            <h1 class="text-center mb-4" style="color:#DA251C;"><b>Donate Now</b></h1>
            <form id="donationForm">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+91-XXXXXXXXXX" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Donation Amount</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary" id="amount10" onclick="setAmount(10, 'amount10')">₹10</button>
                        <button type="button" class="btn btn-outline-primary" id="amount25" onclick="setAmount(25, 'amount25')">₹25</button>
                        <button type="button" class="btn btn-outline-primary" id="amount50" onclick="setAmount(50, 'amount50')">₹50</button>
                        <button type="button" class="btn btn-outline-primary" id="amount100" onclick="setAmount(100, 'amount100')">₹100</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#customAmountModal">Custom Amount</button>
                    </div>
                </div>
                <p>Amount to be Donated: <span style="font-size:larger"><b style="color: #DA251C;" id="amount">--</b><b style="color:#DA251C">Rs</b></span></p>
                <button type="submit" class="btn" style="background-color:#DA251C;color:white;display:block;margin:0 auto">Donate</button>
            </form>

            <!-- Custom Amount Modal -->
            <div class="modal fade" id="customAmountModal" tabindex="-1" aria-labelledby="customAmountModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="customAmountModalLabel">Enter Custom Amount</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="number" class="form-control" id="customAmount" placeholder="Enter amount" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" onclick="setCustomAmount()">Set Amount</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <script>
        function setAmount(amount, buttonId) {
            document.getElementById('amount').innerText = amount;

            // Remove selected class from all buttons
            document.querySelectorAll('.btn-primary').forEach(btn => btn.classList.remove('btn-selected'));

            // Add selected class to the clicked button
            document.getElementById(buttonId).classList.add('btn-selected');
        }

        function setCustomAmount() {
            var customAmount = document.getElementById('customAmount').value;
            document.getElementById('amount').innerText = customAmount;
            var myModal = bootstrap.Modal.getInstance(document.getElementById('customAmountModal'));
            myModal.hide();

            // Remove selected class from all buttons
            document.querySelectorAll('.btn-primary').forEach(btn => btn.classList.remove('btn-selected'));

            // Add selected class to the custom button
            document.querySelector('.btn-secondary').classList.add('btn-selected');
        }

        document.getElementById('donationForm').addEventListener('submit', function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            var data = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                amount: document.getElementById('amount').innerText
            };
            sessionStorage.setItem('Name', data.name);
            sessionStorage.setItem('MobileNo', data.phone);
            sessionStorage.setItem('Amount', data.amount);
            display(); // Trigger Razorpay payment process
        });

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
                    amount: Number(sessionStorage.getItem("Amount"))
                }
            }).done(function (response) {
                response = JSON.parse(response);
                var orderId = response.orderId;
                var options = {
                    "key": "rzp_test_pPwp0WB2oRjxXV", // Enter the Key ID generated from the Dashboard
                    "amount": Number(sessionStorage.getItem("Amount")) * 100, // Amount in currency subunits
                    "currency": "INR",
                    "image": "https://asraaigl.com/wp-content/uploads/elementor/thumbs/Add-a-heading-250-x-100-px-4-qple7gna3d50hrwkxvbmnov8d2bymbp3vln3vidh28.png",
                    "order_id": orderId, // This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                    "handler": function (response) {
                        to_thank_you_page();
                        console.log(response.razorpay_payment_id);
                        console.log(response.razorpay_order_id);
                        console.log(response.razorpay_signature);
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
                        "color": "#DA251C"
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
                });
                rzp1.open();
            });
        }

        function to_thank_you_page() {
            setInterval(() => {
                window.location.href = 'thank_you.php';
            }, 2000);
        }


        function store_data(){
            $.ajax({
                type:'POST',
                url:'/store_data',
                
            })
        }
    </script>
</body>
</html>
