<?php
	session_start();

	$_SESSION['studentlog'] = "";
	$_SESSION['originName'] = "";
	$_SESSION['originNIS'] = "";
	$_SESSION['originNOP'] = "";
	$_SESSION['currentNIN'] = "";
	$_SESSION['currentNOP'] = "";

	session_destroy();

	echo ('<script type="text/javascript">');
	echo ('window.location="index.php";');
	echo ('</script>');
?>