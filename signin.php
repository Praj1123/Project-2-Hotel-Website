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
    die("Connection failed: " . $conn->connect_error);
}

$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Prepare an SQL statement for execution
        $stmt = $conn->prepare("SELECT uniqueId, password FROM users WHERE email = ?");
        
        // Check if the statement preparation failed
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($uniqueId, $hashedPassword);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashedPassword)) {
                $alertMessage = 'Sign in successful';
                $alertType = 'success';
                // Store user ID in local storage
                echo "<script>
                    localStorage.setItem('dntidd', '$uniqueId');
                    setInterval(()=>{
                    const redirectUrl = sessionStorage.getItem('redirectURL') || 'https://asraaigl.com/';
                    sessionStorage.removeItem('redirectURL'); // Clean up storage
                    window.location.href = redirectUrl;
            },2000)
                </script>";
            } else {
                $alertMessage = 'Invalid password';
                $alertType = 'danger';
            }
        } else {
            $alertMessage = 'No account found with this email';
            $alertType = 'danger';
        }

        $stmt->close();
    } else {
        $alertMessage = 'Please fill in all required fields';
        $alertType = 'warning';
    }

    $conn->close();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in</title>
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
    <section class="h-100 gradient-form" style="background-color: #eee;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">
                                    <div class="text-center">
                                        <img src="https://asraaigl.com/wp-content/uploads/elementor/thumbs/Add-a-heading-250-x-100-px-4-qple7gna3d50hrwkxvbmnov8d2bymbp3vln3vidh28.png"
                                            style="width: 185px;" alt="logo">
                                        <h1 class="mt-3 pb-3"><b style="color: #d21c1f;">Log in</b></h1>
                                    </div>
                                    <?php if (!empty($alertMessage)): ?>
                                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $alertMessage; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php endif; ?>

                                    <form method="post">
                                        <div class="form-outline mb-4">
                                            <label class="mb-2" for="email">Email</label>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label class="mb-2" for="password">Password</label>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required />
                                        </div>
                                        <div class="text-center pt-1 mb-2 pb-1">
                                            <button type="submit" class="btn btn-block fa-lg gradient-custom-2 mb-3" style="color: white;"><b>Log in</b></button>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Don't have an account?</p>
                                            <a href="signup.php" class="btn btn-outline-danger">Create new</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Helping Cows, One Step at a Time</h4>
                                    <p class="small mb-0">Our charity is dedicated to improving the lives of cows through rescue, rehabilitation, and advocacy. We work tirelessly to ensure these gentle creatures receive the care and attention they deserve. Join us in our mission to provide a better life for cows and promote their welfare across the community.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
