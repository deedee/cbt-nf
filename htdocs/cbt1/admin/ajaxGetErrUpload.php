<?php
	session_start();

	echo ($_SESSION['uploadError']);
	$_SESSION['uploadError'] = '';
?>