<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Mental Therapy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8; /* Match the background color from register.php */
        }

        .navbar {
            background-color: rgba(74, 144, 226, 0.7); 
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .nav-links {
            display: flex;
            gap: 20px; /* Adds space between links */
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .doctor-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .doctor-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .doctor-info p {
            margin: 0;
            color: white;
            font-weight: bold;
        }

        .welcome {
            text-align: center;
            margin-top: 50px;
        }

        .welcome h1 {
            color: #4a90e2;
        }

        .content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 50px auto;
        }

        .content .left-section {
            flex: 1;
            text-align: left;
            padding-right: 20px; /* Move content slightly more to the left */
        }

        .content p {
            font-size: 1.2em;
            line-height: 1.6;
        }

        .content img {
            flex: 1;
            max-width: 50%;
            border-radius: 10px;
        }

        .footer {
            background-color: rgba(74, 144, 226, 0.7);
            color: white;
            padding: 20px;
            position: fixed;
            width: 100%;
            bottom: 0;
            text-align: center;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('2.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh; 
        }

        .content {
            position: relative;
            text-align: center;
            color: white; 
            padding: 50px;
        }

        .text-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); 
            background-color: rgba(128, 128, 128, 0.5);
            padding: 20px;
            border-radius: 10px;
            color: white;
        }

        h1, p {
            margin: 0 0 15px;
        }

        /* Centered Alert Box styling */
        .alert-box {
            background-color: #ffcc00;
            color: black;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .alert-box .close-btn {
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            color: black;
            margin-top: 15px;
        }

        .alert-box .close-btn:hover {
            color: red;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <!-- Doctor info on the right -->
        <div class="doctor-info">
            <img src="1.webp" alt="Doctor Image">
            <p>Mental Therapy</p>
        </div>
        <!-- Navigation links on the left -->
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#">Therapists</a>
            <a href="#">Sessions</a>
            <a href="#">Resources</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
 
    <div class="welcome">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Your mental health matters to us.</p>
    </div>

    <div class="content">
        <div class="text-overlay">
            <p>
                Thank you for choosing our platform. Here, you will find resources to improve your mental health, 
                connect with therapists, and participate in discussions with people like you.
            </p>
        </div>
    </div>

    <div class="footer">
        &copy; 2024 Mental Therapy. All rights reserved.
    </div>

     <!-- Alert box -->
     <div class="alert-box" id="alertBox">
        <p>Remember to set a new password every 90 days to protect your privacy.</p>
        <button class="close-btn" onclick="closeAlert()">✖</button>
    </div>
    <script>
   // Close the alert box
       function closeAlert() {
            var alertBox = document.getElementById('alertBox');
            alertBox.style.display = 'none';
        }      
    </script>

    <script>
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
</body>
</html>
