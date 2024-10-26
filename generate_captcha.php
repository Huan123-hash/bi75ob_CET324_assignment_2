<?php
session_start();

// Generate a random 4-digit captcha
$captcha_code = rand(1000, 9999);
$_SESSION['captcha'] = $captcha_code;

// Create an image
$image = imagecreatetruecolor(120, 40);

// Colors
$bg_color = imagecolorallocate($image, 240, 240, 240); // Light background
$text_color = imagecolorallocate($image, 50, 50, 50); // Darker text
$line_color = imagecolorallocate($image, 64, 64, 64); // Line color

// Fill background
imagefilledrectangle($image, 0, 0, 120, 40, $bg_color);

// Add random lines for noise
for ($i = 0; $i < 6; $i++) {
    imageline($image, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $line_color);
}

// Add the captcha text (disordered digits)
$font = __DIR__ . '/arial.ttf'; // Path to a TTF font file
imagettftext($image, 20, rand(-10, 10), rand(10, 20), rand(30, 35), $text_color, $font, $captcha_code);

// Output image
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
