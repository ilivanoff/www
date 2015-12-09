<?php

session_start();

/*
 * ВАЖНО!!!
 * 
 * Название переменной в сессии: PsConstJs.CAPTCHA_FIELD (= FORM_PARAM_PSCAPTURE)
 * Длина текста должна быть: PsConstJs.CAPTCHA_LENGTH
 */

$char = strtoupper(substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4));
$str = rand(1, 7) . rand(1, 7) . $char;
$_SESSION['pscapture'] = $str;


// Set the content type
header('Cache-control: no-cache');
header('Content-type: image/png');

// Create an image from button.png
$image = imagecreatefrompng('button.png');

// Set the font colour
$colour = imagecolorallocate($image, 183, 178, 152);

// Set the font
$font = 'Anorexia.ttf';

// Set a random integer for the rotation between -15 and 15 degrees
$rotate = rand(- 15, 15);

// Create an image using our original image and adding the detail
imagettftext($image, 14, $rotate, 18, 30, $colour, $font, $str);

// Output the image as a png
imagepng($image);
session_write_close();
?>