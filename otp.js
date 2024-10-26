require('dotenv').config();   // Load environment variables from .env
const nodemailer = require('nodemailer');

// Get the email and OTP from the command-line arguments
const toEmail = process.argv[2];
const otp = process.argv[3];

// Function to send OTP email
async function sendOTP(toEmail, otp) {
  let sender = nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: process.env.EMAIL_ADDRESS,
      pass: process.env.EMAIL_PASSWORD,
    },
  });

  // Email options (from, to, subject, body)
  let options_for_mail = {
    from: process.env.EMAIL_ADDRESS,
    to: toEmail,
    subject: 'Mental Therapy Clinic: Your OTP Code',
    text: `Welcome to Mental Therapy! Your OTP code is: ${otp}.It will expire in 2 minutes`,
  };

  try {
    // Send the email
    let info = await sender.sendMail(options_for_mail);
    console.log(`OTP sent: ${otp}`);
    console.log('Email sent: ' + info.response);
  } catch (error) {
    console.error('Error sending OTP:', error);
  }
}

// Call the sendOTP function
sendOTP(toEmail, otp);



