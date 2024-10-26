<?php
session_start();
include 'db.php'; // Database connection

$email_error = ""; // Variable to store email error
$captcha_error = ""; // Variable to store captcha error
$password_error = ""; // Variable to store password error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $entered_captcha = $_POST['captcha'];

    // Check if email ends with @gmail.com
    if (!preg_match("/@gmail\.com$/", $email)) {
        $email_error = "Email must end with @gmail.com";
    }

    // Check if email is already registered
    $email_check_query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($email_check_query);
    if ($result->num_rows > 0) {
        $email_error = "This email is already registered. Please login.";
    }

    // Password strength and validation
    if (!preg_match('/[a-z]/', $password) ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W]/', $password) ||
        strlen($password) < 8) {
        $password_error = "Password must contain at least 8 characters, including a lowercase letter, an uppercase letter, a number, and a special character.";
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $password_error = "Passwords do not match!";
    }

    // Check if captcha is correct
    if ($entered_captcha != $_SESSION['captcha']) {
        $captcha_error = "Invalid captcha. Please try again.";
    }

    // Only proceed if there are no errors
    if (empty($password_error) && empty($captcha_error) && empty($email_error)) {
        // Generate OTP
        $otp = rand(100000, 999999);


        // Store OTP and user data in session
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_time'] = time();  // Store OTP generation time

        // Send OTP using Node.js script
        $command = "node otp.js " . escapeshellarg($email) . " " . escapeshellarg($otp);
        exec($command, $output, $result);

        // Check if OTP was sent successfully
        if ($result === 0) {
            echo "OTP sent successfully! Please check your email.";
            header("Location: verify_otp.php");
            exit();
        } else {
            echo "Failed to send OTP.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>User Registration</title>
    <style>
        /* General page styling */
        body {
            background-color: #f0f4f8;
            font-family: 'Arial', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #4a90e2;
            margin-bottom: 20px;
        }

        label {
            color: #333333;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #357ABD;
        }

        .form-container {
            border-radius: 12px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        }

        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .password-container {
            position: relative;
        }

        .password-container input {
            padding-right: 30px;
        }

        .password-container .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #4a90e2;
        }

        /* Captcha row styling */
        .captcha-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .captcha-row img {
            margin-right: 15px;
        }

        .captcha-row input {
            width: 60%; /* Adjust input width */
        }
    </style>
</head>
<body>
<div class="form-container">
        <h2>Welcome to Register</h2>

        <form action="" method="POST" id="registrationForm">
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" required><br>
            <?php if ($email_error): ?>
                <p class="error"><?php echo $email_error; ?></p>
            <?php endif; ?>

            <label for="password">Password:</label>

            <div class="password-container">
                <input type="password" name="password" id="password" required onkeyup="checkPasswordStrength()">
                <span class="toggle-password" onclick="togglePasswordVisibility('password')"><i class="fas fa-eye"></i></span>
                
            </div>
            <p id="strengthMessage" class="strength-indicator"></p> <!-- Password strength indicator -->
            
            <label for="confirm_password">Confirm Password:</label>
            <div class="password-container">
                <input type="password" name="confirm_password" id="confirm_password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('confirm_password')"><i class="fas fa-eye"></i></span>
            </div>
            <?php if ($password_error): ?>
                <p class="error"><?php echo $password_error; ?></p>
            <?php endif; ?>

            <!-- Captcha image and input on the same row -->
            <div class="captcha-row">
                <img src="generate_captcha.php" alt="Captcha Image">
                <input type="text" name="captcha" required placeholder="Enter captcha">
            </div>
            <?php if ($captcha_error): ?>
                <p class="error"><?php echo $captcha_error; ?></p>
            <?php endif; ?>

            <input type="submit" value="Register">
            <p> <a href="login.php">Already have an account?</a></p>

        </form>
    </div>

    <!-- Password Strength Checker Script -->
    <script>
        function checkPasswordStrength() {
            var password = document.getElementById('password').value;
            var strengthMessage = document.getElementById('strengthMessage');
            var strength = 'Weak';

            var strengthRegex = {
                'Weak': /(?=.{6,})/,
                'Medium': /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/,
                'Strong': /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W])(?=.{8,})/
            };

            if (strengthRegex.Strong.test(password)) {
                strength = 'Strong';
                strengthMessage.style.color = 'green';
            } else if (strengthRegex.Medium.test(password)) {
                strength = 'Medium';
                strengthMessage.style.color = 'orange';
            } else if (strengthRegex.Weak.test(password)) {
                strength = 'Weak';
                strengthMessage.style.color = 'red';
            }

            strengthMessage.innerText = 'Password strength: ' + strength;
        }
        // Clear form fields on page reload
        window.onload = function() {
            document.getElementById("registrationForm").reset();
        };
        
          // Clear form fields on page reload (if you have forms in the home page)
    window.onload = function() {
        var form = document.getElementById("registrationForm");
        if (form) {
            form.reset();
        }
    };

 // Prevent form resubmission on page reload
 if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
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
        var icon = field.nextElementSibling.querySelector('i'); // Get the icon element

        if (field.type === "password") {
            field.type = "text";
            icon.classList.remove('fa-eye'); // Remove eye icon
            icon.classList.add('fa-eye-slash'); // Add eye-slash icon
        } else {
            field.type = "password";
            icon.classList.remove('fa-eye-slash'); // Remove eye-slash icon
            icon.classList.add('fa-eye'); // Add eye icon
        }
    }
</script>
</body>
</html>
