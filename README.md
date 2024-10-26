# bi75ob_CET324_assignment_2
mental-therapy-authentication
#Mental Therapy Clinic - User Authentication System

## Description
This project is a user authentication system for the Mental Therapy Clinic application. It includes user registration, login, CAPTCHA verification, two-factor authentication (2FA) with OTP, and logout functionality. The project ensures secure access and enhances user security with additional authentication layers.

## Installation

### Requirements
- PHP
- MySQL
- Node.js (for OTP)
- Required PHP extensions: MySQLi, session handling

### Setup Instructions
1. Clone this repository:
   
   git clone https://github.com/Huan123-hash/bi75ob_CET324_assignment_2
   

2. Configure environment variables in the `.env` file:
   
   EMAIL_ADDRESS=your_email@gmail.com
   EMAIL_PASSWORD=your_email_password
   

3. Set up the database:
   - Import the SQL script (not provided here) to create necessary tables and structure for user data.

4. Install Node.js dependencies for OTP:
   
   npm install dotenv nodemailer
   

## Usage

### User Registration
- The `register.php` file allows new users to register by creating an account, which requires CAPTCHA validation and OTP verification.

### Login and Verification
- `login.php`: Handles user login.
- `generate_captcha.php`: Generates CAPTCHA for user registration.
- `verify_mfa.php` and `verify_otp.php`: Used to verify OTP sent to the user via email.

### OTP Generation
- `otp.js`: This JavaScript file sends an OTP email to the user with Node.js and `nodemailer` configured through environment variables.

### Logout
- `logout.php`: Ends the session and logs out the user.

## Features
- CAPTCHA and password validation on registration
- Two-factor authentication using email-based OTP
- Session management and logout functionality
- Secure environment variable handling for sensitive information

## Project Structure
- `db.php`: Handles database connections.
- `.env`: Stores environment variables like email credentials.
- `generate_captcha.php`: Creates CAPTCHA images for registration.
- `otp.js`: Sends OTP to the user's email for 2FA.
- `verify_mfa.php` and `verify_otp.php`: Handle multi-factor authentication and OTP verification.


## Contact
For questions, please contact me at yinaye36490@gmail.com.


This README provides instructions, file details, and descriptions to guide new users or collaborators through setup and use.
