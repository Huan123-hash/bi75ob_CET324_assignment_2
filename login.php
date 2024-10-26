<?php 
session_start();
include 'db.php'; // Database connection

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $email = $_POST['username']; // Assuming 'username' is the email in this context
    $password = $_POST['password'];

    // Check if user exists and password is correct
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, check password
        $user = $result->fetch_assoc();
        
        // Check password (assuming hashed password storage)
        if (password_verify($password, $user['password'])) {
            // Password is correct, generate OTP for MFA

            $otp = rand(100000, 999999); // Generate a 6-digit OTP
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_time'] = time(); // Store the OTP generation time
            $_SESSION['email'] = $email; // Store email in session, not username

            // Send OTP to user's email using Node.js
            $command = "node otp.js " . escapeshellarg($email) . " " . escapeshellarg($otp);
            exec($command, $output, $result);

            // Redirect to OTP verification page
            header("Location: verify_mfa.php");
            exit();
        } else {
            // Incorrect email or password
            $error_message = "Invalid email or password. Please try again.";
        }
    } else {
        // Incorrect email or password
        $error_message = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Login to Mental Therapy</title>
    <style>
        /* General page styling */
        body {
            background-color: #f0f4f8;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        h2 {
            color: #4a90e2;
            margin-bottom: 10px; /* Adjust margin for spacing */
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            background-color: #357ABD;
        }

        .error {
            color: red;
            margin: 10px 0;
        }

         /* Password container for eye icon */
         .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            padding-right: 30px; /* Add space for eye icon */
        }

        .password-container .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #4a90e2;
        }
        
    </style>
</head>
<body>

<div class="login-container">
    <img src="1.webp" alt="Doctor Logo">
    
    <h2>Login to Mental Therapy</h2>

    <!-- Display error message here -->
    <?php if ($error_message): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="username">Username :</label>
        <input type="text" id="username" name="username" placeholder="Enter your Gmail address" required>

        <label for="password">Password:</label>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('password')"></i>
        </div>


        <button type="submit" class="btn">Login</button>
        <p> <a href="register.php">Don't Have an account?</a></p>
    </form>
</div>

<script>
    // Prevent form resubmission on page reload
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Clear form fields on page reload (if you have forms in the home page)
    window.onload = function() {
        var form = document.getElementById("registrationForm");
        if (form) {
            form.reset();
        }
    };
</script>

 <!-- Prevent back button caching -->
 <script type="text/javascript">
        function preventBack() { window.history.forward() };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null };
</script>

<script>
    
    function togglePasswordVisibility(id) {
        var field = document.getElementById(id);
        var icon = field.nextElementSibling;

        if (field.type === "password") {
            field.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>


</body>
</html>
