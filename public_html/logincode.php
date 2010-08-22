<?php

require_once('inc.config.php');

// $code = mysql_result(mysql_query("SELECT code FROM $TABLE[logincodes] WHERE id='".$_GET['cid']."';"),0,'code');
$code = " ".rand(11111,99999);
$_SESSION['ps_logincode'] = $code;


$plaatje = ImageCreateTrueColor(84,30);

$color_border = ImageColorAllocate($plaatje,0,0,0);
$color_bg = ImageColorAllocate($plaatje,255,255,255);
$color_text = ImageColorAllocate($plaatje,0,0,0);
ImageFilledRectangle($plaatje,0,0,84,30,$color_border);
ImageFilledRectangle($plaatje,1,1,82,28,$color_bg);

for ($i=0;$i<strlen($code);$i++)
{
	$fontnum = rand(2,5);
	$color_text = ImageColorAllocate($plaatje,rand(50,205),rand(50,205),rand(50,205));
	$x = 4+10*$i;
	$y = rand(0,12);
	imageString($plaatje,$fontnum,$x,$y,$code[$i],$color_text);
}

// Twee lijnen voor de onduidelijkheid
ImageLine($plaatje, rand(0,84),rand(0,30), rand(0,84),rand(0,30), $color_border);
ImageLine($plaatje, rand(0,84),rand(0,30), rand(0,84),rand(0,30), $color_border);

Header("Content-type: image/jpeg");
ImagePNG($plaatje);
ImageDestroy($plaatje);

?>