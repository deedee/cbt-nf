<?php

	session_start();

	function acakCaptcha() {
	    $alphabet = "abdefghjkmnptwyABDEFHJKLMNPQRTWY3478";
		$pass = array(); 
	 
	   	$panjangAlpha = strlen($alphabet) - 1; 
	    for ($i = 0; $i < 5; $i++) {
	        $n = rand(0, $panjangAlpha);
	        $pass[] = $alphabet[$n];
	    }

	    $_SESSION["captchaCode"] = implode($pass);

	    //ubah array menjadi string
	    return implode(" ",$pass); 
	}

	 // untuk mengacak captcha
	$code = acakCaptcha();	

	//lebar dan tinggi captcha
	$wh = imagecreatetruecolor(114, 32);

	//background color
	$bgc = imagecolorallocate($wh, 90, 127, 190);

	//text color
	$fc = imagecolorallocate($wh, 250, 240, 230);
	imagefill($wh, 0, 0, $bgc);

	//( $image , $fontsize , $string , $fontcolor )
	imagestring($wh, 5, 17, 8,  $code, $fc);

	//buat gambar
	header('content-type: image/jpg');
	imagejpeg($wh);
	imagedestroy($wh);

?>