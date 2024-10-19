<?php
// In another PHP file
include 'logger.php'; 
include 'config.php';
session_start();




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- sweet alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- aos animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <!-- loading bar -->
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="./css/flash.css">
    <title>Hotel blue bird</title>
</head>

<body>
    <!--  carousel -->
    <section id="carouselExampleControls" class="carousel slide carousel_section" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="carousel-image" src="./image/hotel1.jpg">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel2.jpg">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel3.jpg">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel4.jpg">
            </div>
        </div>
    </section>

    <!-- main section -->
    <section id="auth_section">

        <div class="logo">
            <img class="bluebirdlogo" src="./image/bluebirdlogo.png" alt="logo">
            <p>BLUEBIRD</p>
        </div>

        <div class="auth_container">
            <!--============ login =============-->

            <div id="Log_in">
                <h2>Log In</h2>
                <div class="role_btn">
                    <div class="btns active">User</div>
                    <div class="btns">Staff</div>
                </div>

 <!-- // ==userlogin== -->
 <?php


// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// Initialize login attempt variables if not already set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['login_block_time'])) {
    $_SESSION['login_block_time'] = 0;
}

// Set the maximum login attempts and block duration
$maxAttempts = 2;
$blockDuration = 300; // Block duration in seconds (5 minutes)

if (isset($_POST['user_login_submit'])) {
    // Check if the user is currently blocked
    if (time() < $_SESSION['login_block_time']) {
        $remainingBlockTime = $_SESSION['login_block_time'] - time();
        echo "<script>swal({
            title: 'Too many failed login attempts. Please try again after " . ceil($remainingBlockTime / 60) . " minutes.',
            icon: 'error',
        });</script>";
    } else {
        // Reset login attempts after the block period has passed
        $_SESSION['login_attempts'] = 0;

        // Sanitize user input
        $Email = filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL);
        $Password = $_POST['Password'];

        // Verify reCAPTCHA
        if (isset($_POST['g-recaptcha-response'])) {
            $recaptchaSecretKey = '6Lec-icqAAAAAPSJuF7JScv6FC8MVybGowxB2w_h'; // Replace with your actual secret key
            $recaptchaResponse = $_POST['g-recaptcha-response'];
            $remoteIp = $_SERVER['REMOTE_ADDR'];
            $recaptchaVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecretKey&response=$recaptchaResponse&remoteip=$remoteIp";

            $response = file_get_contents($recaptchaVerifyUrl);
            $responseKeys = json_decode($response, true);

            if (intval($responseKeys["success"]) !== 1) {
                echo "<script>swal({
                    title: 'reCAPTCHA verification failed',
                    icon: 'error',
                });</script>";
            } else {
                writelog("recaptcha verification successful");
                // Proceed with login if reCAPTCHA verification was successful
                $stmt = $conn->prepare("SELECT * FROM signup WHERE Email = ? AND Password = BINARY ?");
                
                // Bind parameters (s for string, s for the password)
                $stmt->bind_param("ss", $Email, $Password);
                
                // Execute the statement
                $stmt->execute();
                
                // Get the result
                $result = $stmt->get_result();
                writelog("login attempt for $Email");
                if ($result->num_rows > 0) {
                    // Successful login
                    writelog("login successful for $Email");
                    $_SESSION['usermail'] = $Email;
                    $_SESSION['login_attempts'] = 0; // Reset the login attempts on successful login
                    $_SESSION['login_block_time'] = 0; // Clear any block time
                    header("Location: home.php");
                    exit(); // Ensure no further code execution
                } else {
                    // Failed login attempt
                    $_SESSION['login_attempts'] += 1; // Increment the login attempts counter
                    if ($_SESSION['login_attempts'] >= $maxAttempts) {
                        $_SESSION['login_block_time'] = time() + $blockDuration; // Set block time
                        echo "<script>swal({
                            title: 'Too many failed login attempts. You are blocked for 5 minutes.',
                            icon: 'error',
                        });</script>";
                    } else {
                        echo "<script>swal({
                            title: 'Invalid email or password',
                            icon: 'error',
                        });</script>";
                        writelog("login failed for $Email");
                    }
                }
                
                // Close the statement
                $stmt->close();
            }
        } else {
            echo "<script>swal({
                title: 'Please complete the reCAPTCHA',
                icon: 'error',
            });</script>";
            
        }
    }
}
?>




                <form class="user_login authsection active" id="userlogin" action="" method="POST">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="Username" placeholder=" ">
                        <label for="Username">Username</label>
                    </div>
                    <div class="form-floating">
                        <input typuser_logine="email" class="form-control" name="Email" placeholder=" ">
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" ">
                        <label for="Password">Password</label>
                    </div>
                    <div class="g-recaptcha" data-sitekey="6Lec-icqAAAAABjlGZEmL380JCubm3oR9Fx3-hsS"></div>

                    <button type="submit" name="user_login_submit" class="auth_btn">Log in</button>

                    <div class="footer_line">
                        <h6>Don't have an account? <span class="page_move_btn" onclick="signuppage()">sign up</span></h6>
                    </div>
                </form>
                
                <!-- == Emp Login == -->
                <?php              
                    if (isset($_POST['Emp_login_submit'])) {
                        $Email = $_POST['Emp_Email'];
                        $Password = $_POST['Emp_Password'];

                                    // Verify reCAPTCHA
                $recaptchaSecret = '6Lec-icqAAAAAPSJuF7JScv6FC8MVybGowxB2w_h'; // Replace with your actual secret key
                $recaptchaResponse = $_POST['g-recaptcha-response'];
                $remoteIp = $_SERVER['REMOTE_ADDR'];

                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse&remoteip=$remoteIp");
                $responseKeys = json_decode($response, true);

                if (intval($responseKeys["success"]) !== 1) {
                    echo "<script>swal({
                        title: 'reCAPTCHA verification failed',
                        icon: 'error',
                    });</script>";
                }
                else {
                    // Prepare the SQL statement
                    $stmt = $conn->prepare("SELECT * FROM emp_login WHERE Emp_Email = ? AND Emp_Password = BINARY ?");
                
                    // Bind parameters (s for string, s for the password)
                    $stmt->bind_param("ss", $Email, $Password);
                
                    // Execute the statement
                    $stmt->execute();
                
                    // Get the result
                    $result = $stmt->get_result();
                
                    if ($result->num_rows > 0) {
                        $_SESSION['usermail'] = $Email;
                        $Email = "";
                        $Password = "";
                        header("Location: admin/admin.php");
                    } else {
                        echo "<script>swal({
                            title: 'Something went wrong',
                            icon: 'error',
                        });
                        </script>";
                    }
                
                    // Close the statement
                    $stmt->close();
                }
                
                    }
                ?> 
                <form class="employee_login authsection" id="employeelogin" action="" method="POST">
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Emp_Email" placeholder=" ">
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Emp_Password" placeholder=" ">
                        <label for="floatingPassword">Password</label>
                    </div>
                    <div class="g-recaptcha" data-sitekey="6Lec-icqAAAAABjlGZEmL380JCubm3oR9Fx3-hsS"></div>
                    <button type="submit" name="Emp_login_submit" class="auth_btn">Log in</button>
                </form>
                
            </div>

 <!--============ signup =============-->
<?php

if (isset($_POST['user_signup_submit'])) {
    $Username = $_POST['Username'];
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];
    $CPassword = $_POST['CPassword'];
    
    // Regular expressions for validation
    $usernamePattern = "/^[a-zA-Z0-9]{3,}$/"; // Only letters and numbers, min 3 characters
    $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/"; // Standard email pattern
    $passwordPattern = "/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/";  //more than 8 characters, at least ,
    //one letter and one number

    // reCAPTCHA verification
    $recaptchaSecret = '6Lec-icqAAAAAPSJuF7JScv6FC8MVybGowxB2w_h'; // Replace with your secret key
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $remoteIp = $_SERVER['REMOTE_ADDR'];
    
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse&remoteip=$remoteIp");
    $responseKeys = json_decode($response, true);
    
    // Validate input fields
    if (intval($responseKeys["success"]) !== 1) {
        echo "<script>swal({
            title: 'reCAPTCHA verification failed',
            icon: 'error',
        });</script>";
    } else {
        if ($Username == "" || $Email == "" || $Password == "") {
            echo "<script>swal({
                title: 'Fill the proper details',
                icon: 'error',
            });</script>";
        } elseif (!preg_match($usernamePattern, $Username)) {
            echo "<script>swal({
                title: 'Invalid username (only letters and numbers, min 3 characters)',
                icon: 'error',
            });</script>";
        } elseif (!preg_match($emailPattern, $Email)) {
            echo "<script>swal({
                title: 'Invalid email format',
                icon: 'error',
            });</script>";
        } elseif (!preg_match($passwordPattern, $Password)) {
            echo "<script>swal({
                title: 'Password must be at least 8 characters long and contain at least one letter and one number',
                icon: 'error',
            });</script>";
        } else {
            if ($Password == $CPassword) {
                $sql = "SELECT * FROM signup WHERE Email = '$Email'";
                $result = mysqli_query($conn, $sql);

                if ($result->num_rows > 0) {
                    echo "<script>swal({
                        title: 'Email already exists',
                        icon: 'error',
                    });</script>";
                } else {
                    // Hash the password before inserting it
                    $hashedPassword = password_hash($Password, PASSWORD_BCRYPT);

                    // Insert the hashed password into the database
                    $sql = "INSERT INTO signup (Username, Email, Password) VALUES ('$Username', '$Email', '$hashedPassword')";
                    $result = mysqli_query($conn, $sql);

                    if ($result) {
                        $_SESSION['usermail'] = $Email;
                        $Username = "";
                        $Email = "";
                        $Password = "";
                        // Success message
                        echo "<script>swal({
                            title: 'Signup successful!',
                            text: 'Your account has been created successfully.',
                            icon: 'success',
                        }).then(function() {
                            window.location = 'logger.php'; // Redirect to login page after confirmation
                        });</script>";
                         echo "<script>swal({
                            title: 'Signup successful!',
                            text: 'Your account has been created successfully.',
                            icon: 'success',
                        }).then(function() {
                            window.location = 'logger.php'; // Redirect to login page after confirmation
                        });</script>";
                        
                    } 
                    
                    else {
                        echo "<script>swal({
                            title: 'Something went wrong',
                            icon: 'error',
                        });</script>";
                    }
                }
            } else {
                echo "<script>swal({
                    title: 'Passwords do not match',
                    icon: 'error',
                });</script>";
            }
        }
    }
}
?>



            <div id="sign_up">
                <h2>Sign Up</h2>

                <form class="user_signup" id="usersignup" action="" method="POST">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="Username" placeholder=" ">
                        <label for="Username">Username</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" ">
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" ">
                        <label for="Password">Password</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="CPassword" placeholder=" ">
                        <label for="CPassword">Confirm Password</label>
                    </div>
                    <div class="g-recaptcha" data-sitekey="6Lec-icqAAAAABjlGZEmL380JCubm3oR9Fx3-hsS"></div>
                    <button type="submit" name="user_signup_submit" class="auth_btn">Sign up</button>

                    <div class="footer_line">
                        <h6>Already have an account? <span class="page_move_btn" onclick="loginpage()">Log in</span></h6>
                    </div>
                </form>
            </div>
    </section>
</body>


<script src="./javascript/index.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- aos animation-->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init();
</script>
</html>

