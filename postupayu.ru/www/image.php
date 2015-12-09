<?php
session_start();

$code1 = md5(microtime() * mktime());
$code1 = substr($code1, 0, 5);
$_SESSION['regCode'] = $code1;

//zadaem pole
$im = imagecreatetruecolor(30*strlen($code1), 40); //osnovnoe pole
$clr1=imagecolorallocate($im, rand(210,255), rand(210,255), rand(210,255));
imagefill($im,0,0,$clr1);

$tmp = imagecreatetruecolor(20, 20); //pole dlya raboty
for($i=0;$i < strlen($code1);$i++ )
{
    $cod_char=$code1{$i};
    $a=rand(-35,35); if($a < 0) $a=$a + 360;
    // echo "angle=${a}<br>";
    $tmp = imagecreatetruecolor(20, 20);
    imagefill($tmp,0,0,$clr1);
    $clr=imagecolorallocate($tmp, rand(0,120), rand(0,120), rand(0,120));
    imagechar($tmp, 5, 5, 0, $cod_char, $clr);
    $tmp=imagerotate($tmp,$a,$clr1);
    $xx=rand(20,30);$yy=rand(20,30); //$l=$xx+$l;
    //imagecopymerge ( $im, $tmp, $i*20, 0, 0, 4, 30, 30, rand(90,100));
    imagecopyresampled($im, $tmp, $i*30, 4, 0, 0, $xx, $yy, 20,20);
};

//generim musor
/*
for($i=0;$i<1000;$i++)
{
    $clr=imagecolorallocate($im, rand(0,200), rand(0,200), rand(0,200));
    imagesetpixel ( $im, rand(0,30*strlen( $code1)), rand(0,40), $clr );
};
 * 
 */
header('Content-type: image/png');
imagepng($im);
session_write_close();

/*
$code = md5(microtime() * mktime());
$md5 = substr($code, 0, 5);
$_SESSION['regCode'] = $md5;
for($i = 0; $i < strlen($md5); $i++) {
    $arr[$i] = substr($_SESSION['regCode'],$i,1);
}
$im = imagecreate(130,40);
imagecolorallocate($im,255,255,255);
$a = 0;
for($i = 0; $i < 7;$i++)
{
    $color=imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
    imagestring($im,3,$a+=15,0,$arr[$i],$color);
}
header("Content-type: image/jpeg");
imagejpeg($im,'',100);
 */


?> 