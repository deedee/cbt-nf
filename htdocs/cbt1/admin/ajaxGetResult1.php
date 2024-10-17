<?php

	ini_set('max_execution_time', 1800);  //in seconds
	require_once('../koneksi_db.php');
	
	$soalKode = $_POST['kto'];
	$jur = $_POST['kjur'];		//jurusan siswa IPA atau IPS, IPC gak perlu krna otomatis terhitung
	
	//pilih dan ambil kunci jawaban
	$pilihSoal = "SELECT indexBidStudi, kelompok, bidStudiTabel, namaBidStudi, nomorAwalPerBS, banyakItemNomor, kunciJawaban FROM naskahsoal WHERE kodeSoal='$soalKode'";
	$ambilKunci = mysqli_query($con,$pilihSoal);
	$dapatKunci = mysqli_fetch_array($ambilKunci);

	$keHari = $dapatKunci['indexBidStudi'];		//tes ke-berapa yg mau diproses hasilnya, identik dengan index bid studi
	$nItem = $dapatKunci['banyakItemNomor'];
	$klpSoal = $dapatKunci['kelompok'];
	$tabelBidStudi = $dapatKunci['bidStudiTabel'];
	$nBidStudi = substr_count($tabelBidStudi,"+");
	$nBidStudi++;
	$studiBidangNama = $dapatKunci['namaBidStudi'];
	$noAwalBS = $dapatKunci['nomorAwalPerBS'];
	$answerK = $dapatKunci['kunciJawaban'];
	$answerKey = str_replace("...","",$answerK);

	$jmlDiproses = 0;

	$cariYgLumSelesai = mysqli_query($con,"SELECT nomorInduk, nomorPeserta, jurusan, acakSoal$keHari, tmpAnswer$keHari FROM absensirecord WHERE (jurusan='IPC' OR jurusan='$jur') AND acakSoal$keHari!='' AND tmpAnswer$keHari!=''");

	while ($ygLumSelesai = mysqli_fetch_array($cariYgLumSelesai))
	{
		$jmlDiproses++;

		$soalAcak = $ygLumSelesai['acakSoal'.$keHari];
		$butirIsian = $ygLumSelesai['tmpAnswer'.$keHari];
		//$untaiJwb = substr($butirIsian,2);
		$untaiJwb = $butirIsian;
		$dataNoInduk = $ygLumSelesai['nomorInduk'];
		$dataNoPeserta = $ygLumSelesai['nomorPeserta'];
		$dataProgram = $ygLumSelesai['jurusan'];

		//nol-in time
		mysqli_query($con,"UPDATE aruntimer SET runTime$keHari=0 WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'");

		//ambil sistem scoringnya
		$ambilDataSystem = mysqli_query($con,"SELECT * FROM datasystem WHERE id=1");
		$dapatDataSystem = mysqli_fetch_array($ambilDataSystem);
		$scB = $dapatDataSystem['sB'];
		$scS = $dapatDataSystem['sS'];
		$scK = $dapatDataSystem['sK'];
		$sekala = $dapatDataSystem['skala'];


		$kunciKe = explode(",", $answerKey);
		$numberItem = count($kunciKe);			//banyak butir soal semua bid studi jg bisa diitung dari array $kunciKe;
		$soalTeracak = explode(",", $soalAcak);
		$jwbKe = explode("|", $untaiJwb);
		$isianUrut = explode("|", $untaiJwb);

		if ($nBidStudi==1)		//utk yang satu kode soal hanya satu bid studi
		{
			$noJwbKosong = ""; $noJwbBenar = ""; $noJwbSalah = "";
			$jwbKosong = 0; $jwbBenar = 0; $jwbSalah = 0;

			for ($q=1; $q<=$numberItem; $q++)
			{
				if ($q<10) { $r = '0'.$q; } else { $r=$q; }
				if ($soalTeracak[$q-1]<10) { $s = '0'.$soalTeracak[$q-1]; } else { $s = $soalTeracak[$q-1]; }

				if ($jwbKe[$q-1] == " ")
				{
					$noJwbKosong = $noJwbKosong.$r."-".$s.",";
					$jwbKosong++;
				}
				else if ($jwbKe[$q-1] == $kunciKe[$soalTeracak[$q-1]-1])
				{
					$noJwbBenar = $noJwbBenar.$r."-".$s.",";
					$jwbBenar++;
				}
				else
				{
					$noJwbSalah = $noJwbSalah.$r."-".$s.",";
					$jwbSalah++;
				}

				if ($jwbKe[$q-1]=='') { $jwbKe[$q-1] = ' '; }

				$isianUrut[$soalTeracak[$q-1]-1] = $jwbKe[$q-1];
			}

			$urutIsi = implode(',',$isianUrut);
			$urutIsian = str_replace(",", "", $urutIsi);

			//$soalBonus = 0;		//belum diimplementasikan

			//proses scoring
			$noJwbKosong = substr($noJwbKosong,0,strlen($noJwbKosong)-1);
			$noJwbBenar = substr($noJwbBenar,0,strlen($noJwbBenar)-1);
			$noJwbSalah = substr($noJwbSalah,0,strlen($noJwbSalah)-1);
			$nilaiMentah = ($jwbBenar*$scB)+($jwbKosong*$scK)+($jwbSalah*$scS);
			$nilaiRerata = $nilaiMentah/($numberItem*$scB);
			$skor = round($nilaiRerata*$sekala, 2);

			if ($dataProgram=='IPA' || $klpSoal=='IPA' || $dataProgram=='IPS' || $klpSoal=='IPS')
			{
				if ($dataProgram=='IPA' || $klpSoal=='IPA') { $dataKlp="IPA"; $namaTabelHasil = "hasilipa"; }
				else if ($dataProgram=='IPS' || $klpSoal=='IPS') { $dataKlp="IPS"; $namaTabelHasil = "hasilips"; }

				$update_record = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta' AND jurusan='$dataKlp'";
				mysqli_query($con,$update_record);

				//ambil nama kolom BS untuk tabel hasilipa / hasilips
				$pilihKolomBS = "SELECT kolomHasil FROM tabelhasil WHERE kelompok='$dataKlp' AND bidStudiTabel='$tabelBidStudi'";
				$ambilDataBS = mysqli_query($con,$pilihKolomBS);
				$dapatDataBS = mysqli_fetch_array($ambilDataBS);
				$colBS = $dapatDataBS['kolomHasil'];

				$colKode = $colBS."_kode";
				$colJawaban = $colBS."_jawaban";
				$colJwbBenar = $colBS."_jwbBenar";
				$colJwbSalah = $colBS."_jwbSalah";
				$colJwbKosong = $colBS."_jwbKosong";
				$colNilMentah = $colBS."_mentah";
				$colScore = $colBS."_score";

				$update_nilai = "UPDATE $namaTabelHasil SET $colKode = $soalKode,
														  $colJawaban = '$untaiJwb',
														  $colJwbBenar = '$noJwbBenar',
														  $colJwbSalah = '$noJwbSalah',
														  $colJwbKosong = '$noJwbKosong',
														  $colNilMentah = $nilaiMentah,
														  $colScore = $skor
														  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con,$update_nilai);
			}
			else
			{
				//runyam dikit, ini soal utk IPA dan IPS alias IPC, jadi harus disimpen di duo tabel IPA dan IPS

				$update_records = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con,$update_records);

				//ambil nama kolom BS untuk tabel hasilipa
				$pilihKolomBS = "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPA' AND bidStudiTabel='$tabelBidStudi'";
				$ambilDataBS = mysqli_query($con,$pilihKolomBS);
				$dapatDataBS = mysqli_fetch_array($ambilDataBS);
				$colBS = $dapatDataBS['kolomHasil'];

				$colKode = $colBS."_kode";
				$colJawaban = $colBS."_jawaban";
				$colJwbBenar = $colBS."_jwbBenar";
				$colJwbSalah = $colBS."_jwbSalah";
				$colJwbKosong = $colBS."_jwbKosong";
				$colNilMentah = $colBS."_mentah";
				$colScore = $colBS."_score";

				$update_nilaiA = "UPDATE hasilipa SET $colKode = $soalKode,
												  $colJawaban = '$untaiJwb',
												  $colJwbBenar = '$noJwbBenar',
												  $colJwbSalah = '$noJwbSalah',
												  $colJwbKosong = '$noJwbKosong',
												  $colNilMentah = $nilaiMentah,
												  $colScore = $skor
												  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con,$update_nilaiA);

				//ambil nama kolom BS untuk tabel hasilips
				$pilihKolomBS = "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPS' AND bidStudiTabel='$tabelBidStudi'";
				$ambilDataBS = mysqli_query($con,$pilihKolomBS);
				$dapatDataBS = mysqli_fetch_array($ambilDataBS);
				$colBS = $dapatDataBS['kolomHasil'];

				$colKode = $colBS."_kode";
				$colJawaban = $colBS."_jawaban";
				$colJwbBenar = $colBS."_jwbBenar";
				$colJwbSalah = $colBS."_jwbSalah";
				$colJwbKosong = $colBS."_jwbKosong";
				$colNilMentah = $colBS."_mentah";
				$colScore = $colBS."_score";

				$update_nilaiS = "UPDATE hasilips SET $colKode = $soalKode,
												  $colJawaban = '$untaiJwb',
												  $colJwbBenar = '$noJwbBenar',
												  $colJwbSalah = '$noJwbSalah',
												  $colJwbKosong = '$noJwbKosong',
												  $colNilMentah = $nilaiMentah,
												  $colScore = $skor
												  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con,$update_nilaiS);
			}
		}
		else //utk yang satu kode soal beberapa bid studi
		{
			$namaPerBS = explode("+", $studiBidangNama);
			$perBS = explode("+", $tabelBidStudi);
			$noAwal = explode("+", $noAwalBS);
			$itemPerBS = explode("+", $nItem);

			for($o=0;$o<$nBidStudi;$o++)
			{ 
				$noJwbKosong = ""; $noJwbBenar = ""; $noJwbSalah = "";
				$jwbKosong = 0; $jwbBenar = 0; $jwbSalah = 0;
				
				for ($q=$noAwal[$o]; $q<=$noAwal[$o]+$itemPerBS[$o]-1; $q++)
				{
					if ($q<10) { $r = '0'.$q; } else { $r = $q; }
					if ($soalTeracak[$q-1]<10) { $s = '0'.$soalTeracak[$q-1]; } else { $s = $soalTeracak[$q-1]; }

					if ($jwbKe[$q-1] == " ")
					{
						$noJwbKosong = $noJwbKosong.$r."-".$s.",";
						$jwbKosong++;
					}
					else if ($jwbKe[$q-1] == $kunciKe[$soalTeracak[$q-1]-1])
					{
						$noJwbBenar = $noJwbBenar.$r."-".$s.",";
						$jwbBenar++;
					}
					else
					{
						$noJwbSalah = $noJwbSalah.$r."-".$s.",";
						$jwbSalah++;
					}

					if ($jwbKe[$q-1]=='') { $jwbKe[$q-1] = ' '; }
					
					$isianUrut[$soalTeracak[$q-1]-1] = $jwbKe[$q-1];
				}

				$urutIsi = implode(',',$isianUrut);
				$urutIsian = str_replace(",", "", $urutIsi);

				//proses scoring
				$noJwbKosong = substr($noJwbKosong,0,strlen($noJwbKosong)-1);
				$noJwbBenar = substr($noJwbBenar,0,strlen($noJwbBenar)-1);
				$noJwbSalah = substr($noJwbSalah,0,strlen($noJwbSalah)-1);
				$nilaiMentah = ($jwbBenar*$scB)+($jwbKosong*$scK)+($jwbSalah*$scS);
				$nilaiRerata = $nilaiMentah/($itemPerBS[$o]*$scB);
				$skor = round($nilaiRerata*$sekala, 2);

				if ($dataProgram=='IPA' || $klpSoal=='IPA' || $dataProgram=='IPS' || $klpSoal=='IPS')
				{
					if ($dataProgram=='IPA' || $klpSoal=='IPA') { $dataKlp="IPA"; $namaTabelHasil = "hasilipa"; }
					else if ($dataProgram=='IPS' || $klpSoal=='IPS') { $dataKlp="IPS"; $namaTabelHasil = "hasilips"; }

					$update_record2 = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta' AND jurusan='$dataKlp'";
					mysqli_query($con,$update_record2);
					
					//ambil nama kolom BS untuk tabel hasilipa / hasilips
					$pilihKolomBS = "SELECT kolomHasil FROM tabelhasil WHERE kelompok='$dataKlp' AND bidStudiTabel='$perBS[$o]'";
					$ambilDataBS = mysqli_query($con,$pilihKolomBS);
					$dapatDataBS = mysqli_fetch_array($ambilDataBS);
					$colBS = $dapatDataBS['kolomHasil'];

					$untaiJwbBS = substr($untaiJwb,2*$noAwal[$o]-2,2*$itemPerBS[$o]-1);
					$colKode = $colBS."_kode";
					$colJawaban = $colBS."_jawaban";
					$colJwbBenar = $colBS."_jwbBenar";
					$colJwbSalah = $colBS."_jwbSalah";
					$colJwbKosong = $colBS."_jwbKosong";
					$colNilMentah = $colBS."_mentah";
					$colScore = $colBS."_score";

					$update_nilai = "UPDATE $namaTabelHasil SET $colKode = $soalKode,
															  $colJawaban = '$untaiJwb',
															  $colJwbBenar = '$noJwbBenar',
															  $colJwbSalah = '$noJwbSalah',
															  $colJwbKosong = '$noJwbKosong',
															  $colNilMentah = $nilaiMentah,
															  $colScore = $skor
															  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
					mysqli_query($con,$update_nilai);
				}
				else
				{	
					$update_record2s = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
					mysqli_query($con,$update_record2s);

					//ambil nama kolom BS untuk tabel hasilipa
					$pilihKolomBS = "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPA' AND bidStudiTabel='$perBS[$o]'";
					$ambilDataBS = mysqli_query($con,$pilihKolomBS);
					$dapatDataBS = mysqli_fetch_array($ambilDataBS);
					$colBS = $dapatDataBS['kolomHasil'];

					$untaiJwbBS = substr($untaiJwb,2*$noAwal[$o]-2,2*$itemPerBS[$o]-1);
					$colKode = $colBS."_kode";
					$colJawaban = $colBS."_jawaban";
					$colJwbBenar = $colBS."_jwbBenar";
					$colJwbSalah = $colBS."_jwbSalah";
					$colJwbKosong = $colBS."_jwbKosong";
					$colNilMentah = $colBS."_mentah";
					$colScore = $colBS."_score";

					$update_nilaiA = "UPDATE hasilipa SET $colKode = $soalKode,
													  $colJawaban = '$untaiJwb',
													  $colJwbBenar = '$noJwbBenar',
													  $colJwbSalah = '$noJwbSalah',
													  $colJwbKosong = '$noJwbKosong',
													  $colNilMentah = $nilaiMentah,
													  $colScore = $skor
													  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
					mysqli_query($con,$update_nilaiA);

					//ambil nama kolom BS untuk tabel hasilips
					$pilihKolomBS = "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPS' AND bidStudiTabel='$perBS[$o]'";
					$ambilDataBS = mysqli_query($con,$pilihKolomBS);
					$dapatDataBS = mysqli_fetch_array($ambilDataBS);
					$colBS = $dapatDataBS['kolomHasil'];

					$untaiJwbBS = substr($untaiJwb,2*$noAwal[$o]-2,2*$itemPerBS[$o]-1);
					$colKode = $colBS."_kode";
					$colJawaban = $colBS."_jawaban";
					$colJwbBenar = $colBS."_jwbBenar";
					$colJwbSalah = $colBS."_jwbSalah";
					$colJwbKosong = $colBS."_jwbKosong";
					$colNilMentah = $colBS."_mentah";
					$colScore = $colBS."_score";

					$update_nilaiS = "UPDATE hasilips SET $colKode = $soalKode,
													  $colJawaban = '$untaiJwb',
													  $colJwbBenar = '$noJwbBenar',
													  $colJwbSalah = '$noJwbSalah',
													  $colJwbKosong = '$noJwbKosong',
													  $colNilMentah = $nilaiMentah,
													  $colScore = $skor
													  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
					mysqli_query($con,$update_nilaiS);
				}

			}

		}

		//update absensiharitespeserta
		$update_absen = "UPDATE absensiharitespeserta SET ipRemote='', lastNum$keHari='1', acakSoal$keHari='', tmpAnswer$keHari='', loginFlag='0', loginKey='0', TOke='0', curTimer='0', TOFinish=$keHari, finishIt='0', msgx_n='0', msgx='', playedAudio='', ragu$keHari='', hariKe$keHari='1' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
		mysqli_query($con,$update_absen);

	}

	echo $jmlDiproses;
?>