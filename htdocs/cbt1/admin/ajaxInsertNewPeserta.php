<?php

	require_once('../koneksi_db.php');
	$nResCek = 0;

	$nisnp = $_POST['pNISN'];
	$npp = $_POST['pNP'];
	$namap = mysqli_escape_string($con, strtoupper($_POST['pNama']));
	$klsp = $_POST['pKls'];
	$jurp = strtoupper($_POST['pJur']);
	$paketp = $_POST['pPaket'];
	$grupp = $_POST['pGrup'];

	//cek dulu apakah nomorInduk sudah ada yg menggunakan
	$cekNoI = mysqli_query($con,"SELECT nomorInduk FROM absensiharitespeserta WHERE nomorInduk='$nisnp' LIMIT 1");
	$resCekNoI = mysqli_num_rows($cekNoI);
	if ($resCekNoI>0) { $nResCek++; }

	//cek dulu apakah nomorPeserta sudah ada yg menggunakan
	$cekNoP = mysqli_query($con,"SELECT nomorPeserta FROM absensiharitespeserta WHERE nomorPeserta='$npp' LIMIT 1");
	$resCekNoP = mysqli_num_rows($cekNoP);
	if ($resCekNoP>0) { $nResCek+=2; }

	if ($nResCek==0)
	{
		//tambah data baru ke absensipeserta
		$pesertaBaru = "INSERT INTO absensiharitespeserta (nomorInduk,nomorPeserta,petaSoal,nama,kelas,jurusan,shiftTes)
						VALUES ('$nisnp', '$npp', '$paketp', '$namap', '$klsp', '$jurp', '$grupp')";
		$tambahPesertaBaru = mysqli_query($con,$pesertaBaru);

		//isi untuk tabel absensirecord
		if ($jurp=='IPA' OR $jurp=='IPC')
		{
			$recordBaru = "INSERT INTO absensirecord (nomorInduk, nomorPeserta, nama, kelas, jurusan)
							VALUES ('$nisnp', '$npp', '$namap', '$klsp', 'IPA')";
			$tambahRecordBaru = mysqli_query($con,$recordBaru);
		}
		if ($jurp=='IPS' OR $jurp=='IPC')
		{
			$recordBaru = "INSERT INTO absensirecord (nomorInduk, nomorPeserta, nama, kelas, jurusan)
							VALUES ('$nisnp', '$npp', '$namap', '$klsp', 'IPS')";
			$tambahRecordBaru = mysqli_query($con,$recordBaru);
		}

		//tambah data baru ke aruntimer
		$timeBaru = "INSERT INTO aruntimer (nomorInduk,nomorPeserta,nama,kelas,jurusan)
					VALUES ('$nisnp', '$npp', '$namap', '$klsp', '$jurp')";
		$tambahTimeBaru = mysqli_query($con,$timeBaru);
		
		//isi untuk tabel hasil IPA / IPS
		if ($jurp=='IPA' OR $jurp=='IPC')
		{
			$KeHasilIPA = "INSERT INTO hasilipa (nomorInduk,nomorPeserta,nama,kelas)
					VALUES ('$nisnp', '$npp', '$namap', '$klsp')";
			$tambahKeHasilIPA = mysqli_query($con,$KeHasilIPA);
		}
		if ($jurp=='IPS' OR $jurp=='IPC')
		{
			$KeHasilIPS = "INSERT INTO hasilips (nomorInduk,nomorPeserta,nama,kelas)
					VALUES ('$nisnp', '$npp', '$namap', '$klsp')";
			$tambahKeHasilIPS = mysqli_query($con,$KeHasilIPS);
		}
	}

	echo $nResCek;
?>