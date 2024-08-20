<?php
// Database configuration
$host = '127.0.0.1';
$db = 'donationDB';
$user = 'root';
$pass = '';

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    $response = [
        'status' => 'error',
        'message' => 'Error connecting to database: ' . $conn->connect_error,
        'alertType' => 'danger'
    ];
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['fullName']) && !empty($_POST['email']) && !empty($_POST['phoneNumber']) && !empty($_POST['password'])) {
        $fullName = trim($_POST['fullName']);
        $email = trim($_POST['email']);
        $phoneNumber = trim($_POST['phoneNumber']);
        $password = trim($_POST['password']);

        // Validate email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid email address',
                'alertType' => 'danger'
            ];
            echo json_encode($response);
            exit;
        }

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT uniqueId FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response = [
                'status' => 'error',
                'message' => 'An account with this email already exists',
                'alertType' => 'danger'
            ];
            echo json_encode($response);
            exit;
        }

        // Function to generate a 12-digit alphanumeric ID
        function generateUniqueId($length = 12) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $uniqueId = '';
            for ($i = 0; $i < $length; $i++) {
                $uniqueId .= $characters[rand(0, $charactersLength - 1)];
            }
            return $uniqueId;
        }

        // Generate a unique alphanumeric ID
        $uniqueId = generateUniqueId(); // 12 characters long

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (uniqueId, fullName, email, phoneNumber, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $uniqueId, $fullName, $email, $phoneNumber, $hashedPassword);

        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Sign up successful',
                'alertType' => 'success',
                'uniqueId' => $uniqueId
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error during sign up: ' . $stmt->error,
                'alertType' => 'danger'
            ];
        }

        $stmt->close();
        $conn->close();
        echo json_encode($response);
        exit;
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Please fill in all required fields',
            'alertType' => 'warning'
        ];
        echo json_encode($response);
        exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        .gradient-custom-2 {
            background: #fccb90;
            background: -webkit-linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
            background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }

        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }
    </style>
</head>

<body> 
    <section class="h-100 gradient-form" style="background-color: #eee;position:relative">
        <a href='#' class='btn btn-outline-danger' style='position:absolute;top:50px;left:20px'>< Home</a>
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Empowering Communities Through Education</h4>
                                    <p class="small mb-0">Our organization is committed to enhancing educational
                                        opportunities for underprivileged communities. We provide resources, mentorship,
                                        and support to help individuals achieve their full potential. Our mission is to
                                        foster a culture of learning and growth, ensuring that everyone has access to
                                        the tools they need for success. Join us in making a difference and empowering
                                        future leaders.</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">

                                    <div class="text-center">
                                        <img src="https://asraaigl.com/wp-content/uploads/elementor/thumbs/Add-a-heading-250-x-100-px-4-qple7gna3d50hrwkxvbmnov8d2bymbp3vln3vidh28.png"
                                            style="width: 185px;" alt="logo">
                                        <h1 class="mt-3 pb-3"><b style="color: #d21c1f;">Sign up</b></h1>
                                    </div>

                                    <div id="alertContainer"></div>

                                    <form id="signupForm">
                                        <div class="form-outline mb-4">
                                            <label class="mb-2">Full Name</label>
                                            <input type="text" id="fullName" name="fullName" class="form-control"
                                                placeholder="Enter your Full Name" required />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="mb-2">Email</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="Enter your email" required />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="mb-2">Mobile No.</label>
                                            <input type="text" id="phoneNumber" name="phoneNumber" class="form-control"
                                                placeholder="Enter your Mobile Number" required />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <label class="mb-2">Password</label>
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Enter Your Password" required />
                                        </div>

                                        <div class="text-center pt-1 mb-2 pb-1">
                                            <button type="submit" class="btn btn-block fa-lg gradient-custom-2 mb-3"
                                                style="color: white;"><b>Sign up</b></button>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Already Have an account?</p><a href="signin.php">Sign
                                                in</a>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('signupForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission
                
                var formData = new FormData(this);
                
                fetch('signup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    var alertContainer = document.getElementById('alertContainer');
                    alertContainer.innerHTML = '';
                    
                    var alert = document.createElement('div');
                    alert.className = 'alert alert-' + data.alertType;
                    alert.role = 'alert';
                    alert.textContent = data.message;
                    alertContainer.appendChild(alert);
                    
                    if (data.status === 'success') {
                        localStorage.setItem('dntidd', data.uniqueId);
                        setInterval(() => {
                            window.location.href='https://asraaigl.com/';
                        },3000);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>

</html>
