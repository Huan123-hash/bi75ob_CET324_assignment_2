<?php
session_start();  // Start the session at the very beginning, before any output

include 'db.php'; // Include the database connection

$error_message = ""; // Initialize the error message
// Handle OTP resend request
if (isset($_POST['resend_otp'])) {
    if (!isset($_SESSION['email'])) {
        $error_message = "Session expired or email not found. Please register again.";
    } else {
        // Generate new OTP and store in session
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_time'] = time(); // Reset OTP generation time

        // Send OTP using Node.js script
        $command = "node otp.js " . escapeshellarg($_SESSION['email']) . " " . escapeshellarg($otp);
        exec($command, $output, $result);

        if ($result === 0) {
            $error_message = "A new OTP has been sent to your email.";
        } else {
            $error_message = "Failed to send OTP.";
        }
    }
}

// Handle OTP form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp'])) {
    // Check if OTP and OTP generation time are set
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_time'])) {
        $error_message = "OTP not found or session expired. Please request a new OTP.";
    } else {
        $entered_otp = $_POST['otp'];
        $current_time = time();

        // Check if the OTP is expired
        if ($current_time - $_SESSION['otp_time'] > 120) {  // Check if more than 2 minutes passed
            $error_message = "OTP has expired. Please request a new OTP.";
        } elseif ($entered_otp == $_SESSION['otp']) {
            // OTP is correct, register the user in the database

            // Sanitize and hash the password
            $hashed_password = password_hash($_SESSION['password'], PASSWORD_DEFAULT);

            // Prepare SQL query to insert user data into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $_SESSION['username'], $_SESSION['email'], $hashed_password);

                // Execute the query
                if ($stmt->execute()) {
                    $error_message = '<p class="success">Registration successful! Redirecting to login page...</p>';
                    // Redirect to the login page after 3 seconds
                    header("refresh:3;url=login.php");
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Database error: Unable to prepare statement.";
            }
        } else {
            // OTP is incorrect
            $error_message = "Invalid OTP. Please try again.";
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
    <title>Verify OTP</title>
    <style>
        /* General page styling */
        body {
            background-color: #f0f4f8;
            font-family: 'Arial', sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .otp-container {
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
            margin-bottom: 10px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        input[type="text"], input[type="submit"], button {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #cccccc;
    border-radius: 5px;
    box-sizing: border-box;
}

button, input[type="submit"] {
    background-color: #4a90e2;
    color: white;
    cursor: pointer;
}

/* Hover effect for button and submit input */
button:hover, input[type="submit"]:hover {
    background-color: #357ABD; /* Slightly darker blue on hover */
}

        button:disabled {
            background-color: #cccccc;
        }

        .disabled {
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h2>Enter Register OTP sent to email</h2>

    <!-- Display error or success message here -->
    <?php if ($error_message): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="verify_otp.php" method="POST">
        <input type="text" name="otp" placeholder="Enter the OTP" required><br><br>
        <input type="submit" value="Verify OTP">
    </form>

    <!-- Resend OTP button -->
    <form action="verify_otp.php" method="POST">
        <button type="submit" name="resend_otp" id="resendOtpBtn" disabled>Resend OTP</button>
    </form>

    <p id="timer"></p>
    <p> <a href="login.php">Already have an account?</a></p>
</div>

<script>
    // Get the OTP generation time from the server
    const otpGeneratedTime = <?php echo isset($_SESSION['otp_time']) ? time() - $_SESSION['otp_time'] : 0; ?>;
    const resendOtpBtn = document.getElementById("resendOtpBtn");
    const timerDisplay = document.getElementById("timer");

    let countdown = 120 - otpGeneratedTime;

    if (countdown <= 0) {
        // If OTP has already expired, enable the resend button
        resendOtpBtn.disabled = false;
        timerDisplay.innerText = "You can resend the OTP now.";
    } else {
        // Start countdown if OTP is still valid
        const timer = setInterval(() => {
            if (countdown <= 0) {
                clearInterval(timer);
                resendOtpBtn.disabled = false;
                timerDisplay.innerText = "You can resend the OTP now.";
            } else {
                const minutes = Math.floor(countdown / 60);
                const seconds = countdown % 60;
                timerDisplay.innerText = `Resend OTP in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                countdown--;
            }
        }, 1000);
    }
 
     // Prevent form resubmission on page reload
     if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Clear form fields on page reload
        window.onload = function() {
            document.getElementById("registrationForm").reset();
        };
</script>
 <!-- Prevent back button caching -->
 <script type="text/javascript">
        function preventBack() { window.history.forward() };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null };
    </script>
</body>
</html>
