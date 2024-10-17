<?php

	session_start();

	$_SESSION['studentlog'] = "";
	$_SESSION['originName'] = "";
	$_SESSION['originNIS'] = "";
	$_SESSION['originNOP'] = "";
	$_SESSION['currentNIN'] = "";
	$_SESSION['currentNOP'] = "";

	session_destroy();

	require_once('../koneksi_db.php');


	$pilID = $_POST['rID'];

	if ($pilID!="") {
		$cekNoSiswa = mysqli_query($con,"SELECT nomorInduk,nomorPeserta FROM absensiharitespeserta WHERE id = '$pilID' LIMIT 1");
		$idSiswa = mysqli_fetch_array($cekNoSiswa);
		$noInduk = $idSiswa['nomorInduk'];
		$nomorPeserta = $idSiswa['nomorPeserta'];

		mysqli_query($con,"UPDATE absensiharitespeserta SET ipRemote='', piljur1='', piljur2='', piljur3='', loginFlag='0', loginKey='0', curTimer='0', loginError='0', kodeTO='', TOke='0', TOFinish='0', finishIt='0', msgx_n='0', msgx='', playedAudio='',
					 acakSoal1='', lastNum1='1', tmpAnswer1='', ragu1='', acakSoal2='', lastNum2='1', tmpAnswer2='', ragu2='',
					 acakSoal3='', lastNum3='1', tmpAnswer3='', ragu3='', acakSoal4='', lastNum4='1', tmpAnswer4='', ragu4='',
					 acakSoal5='', lastNum5='1', tmpAnswer5='', ragu5='', acakSoal6='', lastNum6='1', tmpAnswer6='', ragu6='',
					 acakSoal7='', lastNum7='1', tmpAnswer7='', ragu7='', acakSoal8='', lastNum8='1', tmpAnswer8='', ragu8='',
					 acakSoal9='', lastNum9='1', tmpAnswer9='', ragu9='', acakSoal10='', lastNum10='1', tmpAnswer10='', ragu10='',
					 hariKe1='0', hariKe2='0', hariKe3='0', hariKe4='0', hariKe5='0', hariKe6='0', hariKe7='0', hariKe8='0', hariKe9='0', hariKe10='0' WHERE nomorInduk='$noInduk' AND nomorPeserta='$nomorPeserta'");

		mysqli_query($con,"UPDATE absensirecord SET acakSoal1='', tmpAnswer1='', ordAnswer1='', acakSoal2='', tmpAnswer2='', ordAnswer2='',
													acakSoal3='', tmpAnswer3='', ordAnswer3='', acakSoal4='', tmpAnswer4='', ordAnswer4='',
													acakSoal5='', tmpAnswer5='', ordAnswer5='', acakSoal6='', tmpAnswer6='', ordAnswer6='',
													acakSoal7='', tmpAnswer7='', ordAnswer7='', acakSoal8='', tmpAnswer8='', ordAnswer8='',
													acakSoal9='', tmpAnswer9='', ordAnswer9='', acakSoal10='', tmpAnswer10='', ordAnswer10=''
													WHERE nomorInduk='$noInduk' AND nomorPeserta='$nomorPeserta'");

		mysqli_query($con,"UPDATE aruntimer SET runTime1='0', runTime2='0', runTime3='0', runTime4='0', runTime5='0', runTime6='0', runTime7='0', runTime8='0', runTime9='0', runTime10='0' WHERE nomorInduk='$noInduk' AND nomorPeserta='$nomorPeserta'");
	}
	else {
		mysqli_query($con,"UPDATE absensiharitespeserta SET ipRemote='', piljur1='', piljur2='', piljur3='', loginFlag='0', loginKey='0', curTimer='0', loginError='0', kodeTO='', TOke='0', TOFinish='0', finishIt='0', msgx_n='0', msgx='', playedAudio='',
					 acakSoal1='', lastNum1='1', tmpAnswer1='', ragu1='', acakSoal2='', lastNum2='1', tmpAnswer2='', ragu2='',
					 acakSoal3='', lastNum3='1', tmpAnswer3='', ragu3='', acakSoal4='', lastNum4='1', tmpAnswer4='', ragu4='',
					 acakSoal5='', lastNum5='1', tmpAnswer5='', ragu5='', acakSoal6='', lastNum6='1', tmpAnswer6='', ragu6='',
					 acakSoal7='', lastNum7='1', tmpAnswer7='', ragu7='', acakSoal8='', lastNum8='1', tmpAnswer8='', ragu8='',
					 acakSoal9='', lastNum9='1', tmpAnswer9='', ragu9='', acakSoal10='', lastNum10='1', tmpAnswer10='', ragu10='',
					 hariKe1='0', hariKe2='0', hariKe3='0', hariKe4='0', hariKe5='0', hariKe6='0', hariKe7='0', hariKe8='0', hariKe9='0', hariKe10='0'");

		mysqli_query($con,"UPDATE absensirecord SET acakSoal1='', tmpAnswer1='', ordAnswer1='', acakSoal2='', tmpAnswer2='', ordAnswer2='',
													acakSoal3='', tmpAnswer3='', ordAnswer3='', acakSoal4='', tmpAnswer4='', ordAnswer4='',
													acakSoal5='', tmpAnswer5='', ordAnswer5='', acakSoal6='', tmpAnswer6='', ordAnswer6='',
													acakSoal7='', tmpAnswer7='', ordAnswer7='', acakSoal8='', tmpAnswer8='', ordAnswer8='',
													acakSoal9='', tmpAnswer9='', ordAnswer9='', acakSoal10='', tmpAnswer10='', ordAnswer10=''");

		mysqli_query($con,"UPDATE aruntimer SET runTime1='0', runTime2='0', runTime3='0', runTime4='0', runTime5='0', runTime6='0', runTime7='0', runTime8='0', runTime9='0', runTime10='0'");

		mysqli_query($con,"UPDATE datasystem SET lastFinish='0000-00-00'");
	}
	
	echo $pilID;

?>