<?php

    session_start();
    require_once('koneksi_db.php');

    //variabel ini akan punya nilai jika peserta masuk dari link login terpusat
    if (isset($_POST['id_A'])) {
        $noid1 = $_POST['id_A'];
        $noid2 = $_POST['id_B'];
    }
    else {
        $noid1='';
        $noid2='';
    }

    //$remIP = $_SERVER['REMOTE_ADDR'];

    //ambil data variabel sekolah
    $cekDataSekolah = mysqli_query($con, "SELECT * FROM datasystem WHERE id = '1'");
    $hasilDataSekolah = mysqli_fetch_array($cekDataSekolah);

    $judulBesar = $hasilDataSekolah['bigTitle'];
    $namaSekolahFront = $hasilDataSekolah['namaSekolah'];
    $bentukPelaksanaan = $hasilDataSekolah['teksPelaksanaan'];
    $showSearch = $hasilDataSekolah['searchNama'];
    $teksno1 = $hasilDataSekolah['labelno1'];
    $teksno2 = $hasilDataSekolah['labelno2'];
    $nteks1 = $hasilDataSekolah['nlabel1'];
    $nteks2 = $hasilDataSekolah['nlabel2'];

    $prefixid1 = $hasilDataSekolah['prefixid1'];
    $prefixid2 = $hasilDataSekolah['prefixid2'];
    $inp2pw = $hasilDataSekolah['label2password'];

    $motoSekolahFront = $hasilDataSekolah['motoSekolah'];
    $showHP = $hasilDataSekolah['tampilHP'];
    $showEmail = $hasilDataSekolah['tampilEmail'];
    $showJur = $hasilDataSekolah['tampilPilJur'];
    $showDoB = $hasilDataSekolah['tampilDoB'];

    if ($noid1 !='') { $prefixid1=$noid1; }
    if ($noid2 !='') { $prefixid2=$noid2; }

    //ambil data admin dari tabel dataadmin
    $cekDataAdmin = mysqli_query($con, "SELECT * FROM dataadmin WHERE id = '1'");
    $hasilDataAdmin = mysqli_fetch_array($cekDataAdmin);
    $adminName = $hasilDataAdmin['nama'];
    $adminNo = $hasilDataAdmin['nokontak'];
    $linkThisGrup = $hasilDataAdmin['grupwa'];

    //timenumeric/////////////////////////////////////////////////
    date_default_timezone_set('Asia/Jakarta');    //set timezone

    $thn_ = date("Y");
    $tah = str_split($thn_);
    $bln_ = date("m")*100;
    $tgl_ = date("d");
    $nKal = ($thn_*10000+$bln_+$tgl_)*1000000;

    $numHour = date("G");
    $numMin = date("i");
    $numSec = date("s");
    $numericTime = $numHour*3600 + $numMin*60 + $numSec;
    $nowTimer = $nKal+$numericTime;

    // variabel idle ini juga ada di file cekAktifitas.php
    $idle = 30;

    //////////////////////////////////////////////////////////////

    //apakah sudah ada session NID dan NPD sebelumnya ?
    if (isset($_SESSION['currentNIN']) && !empty($_SESSION['currentNIN']))
    { $nid = $_SESSION['currentNIN']; } else { $nid = ''; }

    if (isset($_SESSION['currentNOP']) && !empty($_SESSION['currentNOP']))
    { $npd = $_SESSION['currentNOP']; } else { $npd = ''; }

    if (isset($_SESSION['currentNIN']) && !empty($_SESSION['currentNIN']) && isset($_SESSION['currentNOP']) && !empty($_SESSION['currentNOP'])) {
        //cek Peserta ...
        $cekTimer = mysqli_query($con, "SELECT curTimer FROM absensiharitespeserta WHERE nomorInduk='$nid' AND nomorPeserta='$npd' LIMIT 1");
        $hasilCekTimer = mysqli_fetch_array($cekTimer);

        $dTimer = $nowTimer - $hasilCekTimer['curTimer'];
        if ($dTimer > $idle) {
            // klo lebih dari waktu idle brrti keluar dari halaman tes, lngsung arahkan ke halaman testpage
            header("Location: testpage.php");
            exit;
        }
    }
    else { $dTimer = 0; }

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="TO Online CBT BKB Nurul Fikri">
        <meta name="keywords" content="BKB, NF, nurul fikri, nurul, fikri, bimbel, islami, pendidikan, sekolah, cara cepat, to, to online, cbt">

        <link rel="shortcut icon" href="images/nf-favico.ico" type="image/x-icon">
        <link rel="stylesheet" href="w3.css">
        <link rel="stylesheet" type="text/css" href="validationEngine.jquery.css" />
        <link rel="stylesheet" type="text/css" href="jquery-ui-1.8.10.custom.css" />
        <link rel="stylesheet" type="text/css" href="jquery.ui.theme.css" />

        <script type="text/javascript" src="fnAcakSoal.js"></script>
        <script type="text/javascript" src="jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="jquery-ui-1.8.10.custom.min.js"></script>
        <script type="text/javascript" src="jquery.validationEngine.js"></script>
        <script type="text/javascript" src="jquery.validationEngine-en.js"></script>

        <script type="text/javascript">
            $(function() {
                $("#getDoB").datepicker({ minDate: "-30Y", maxDate: "-4Y", changeMonth: true, changeYear: true });
                //jadinya nanti berupa text dg format mm/dd/yyyy
            });

            function shpw() {
                var nopes = document.getElementById("noPeserta");
                if (nopes.type === "password")
                { nopes.type = "text"; } else { nopes.type = "password"; }
            }
        </script>

        <style media="screen" type="text/css">
            <!--
            html
            { box-sizing: border-box; }

            body {
                background: url("images/backg.gif") repeat-x #eee;
                margin:0;
                padding:0;
                border:0;     /* This removes the border around the viewport in old versions of IE */
                min-width:285px;   /* The min-width property does not work in old versions of Internet Explorer */     
            }

            input:not([type=submit]) {
                border: 1px solid #A8DCF1;
                border-radius: 4px;
                color: #527FC9;
                font-family: Arial;
                font-weight: bold;
                padding-left: 4px;
            }

            #rightPanel, #mySidebar :not(INPUT):not(TEXTAREA) {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                -o-user-select: none;
                user-select: none;
            }

            /* respEl = responsive Element */
            @media screen and (max-width: 651px) {
                .respEl {
                display: none;
                }
            }

            -->
        </style>

        <title><?= $titleBarLoginPage; ?></title>
    </head>

    <body class="w3-light-blue" oncontextmenu="return false">

        <!-- rule Modal -->
        <div id="piljurModal" class="w3-modal" style="z-index: 5">
            <div class="w3-modal-content w3-animate-zoom w3-light-grey w3-card-4">
                <header class="w3-container w3-light-blue w3-center w3-padding-small"> 
                    <span onclick="document.getElementById('piljurModal').style.display='none'" class="w3-button w3-hide-large w3-hide-medium w3-light-blue w3-hover-red w3-large w3-display-topleft">&times;</span>
                    <span onclick="document.getElementById('piljurModal').style.display='none'" class="w3-button w3-hide-small w3-light-blue w3-hover-red w3-large w3-display-topright">&times;</span>
                    <h2 class="w3-large"> <strong>Pilihan Prodi</strong> </h2>
                </header>

                <div class="w3-container">
                    <div style="height:13px"></div>
                    <table>
                        <tr>
                            <td style="width: 6em"><span style="color: #3496ee; font-weight: bold;">Kelompok </span></td>
                            <td>
                                <select name="kelompok" id="kelompok" style="border-color: #3496ee; border-radius: 3px; font-weight: bold; color: #555">
                                    <option value="saintek">SAINTEK</option>
                                    <option value="soshum">SOSHUM</option>
                                    <option value="">SAINTEK & SOSHUM&nbsp;&nbsp;</option>
                                </select>
                            </td>
                        </tr>
                        <tr><td style="height: 3px"></td></tr>
                        <tr>
                            <td><span style="color: #3496ee; font-weight: bold;">Jenjang </span></td>
                            <td>
                                <select name="jenjang" id="jenjang" style="border-color: #3496ee; border-radius: 3px; font-weight: bold; color: #555">
                                    <option value="s1">S1</option>
                                    <option value="d4">D4</option>
                                    <option value="">S1 & D4&nbsp;&nbsp;</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <!-- <input class="w3-radio" type="radio" name="kelompok" value="saintek" id="klpsaintek">
                    <label for="klpsaintek">SAINTEK</label>&nbsp;&nbsp;&nbsp;
                    <input class="w3-radio" type="radio" name="kelompok" value="soshum" id="klpsoshum">
                    <label for="klpsoshum">SOSHUM</label>&nbsp;&nbsp;&nbsp;
                    <input class="w3-radio" type="radio" name="kelompok" value="ipc" id="klpipc">
                    <label for="klpipc">Semua</label> -->
                    <div style="height:10px"></div>

                    <label class="w3-text-blue"><b>Nama PTN</b></label>
                    <input class="w3-input" type="text" style="margin-bottom: 5px" placeholder="isi di sini" id="namaptn">
                    <div style="max-height: 200px; overflow-y: auto;">
                        <table><span id="resnamaptn"></span></table>
                    </div>
                    
                    <div style="height:10px"></div>

                    <label class="w3-text-blue"><b>Jurusan</b></label>
                    <input class="w3-input" type="text" style="margin-bottom: 5px" placeholder="isi di sini" id="namajur">
                    <div style="max-height: 150px; overflow-y: auto;">    
                        <table id="resnamajur"></table>
                    </div>
                    
                    <div style="height:10px"></div>

                    <input type="hidden" id="untukpj">
                    <input type="hidden" id="kodejurnya">
                    <button id="btnGetCode" class="w3-btn w3-blue">Ambil kode</button>
                    <br><br>
                </div>
            </div>
            <br><br>
        </div>

        <!-- cari data Modal -->
        <div id="cariDataModal" class="w3-modal" style="z-index: 5;">
            <div class="w3-modal-content w3-animate-top w3-light-grey w3-card-4">
                <header class="w3-container w3-light-blue w3-center w3-padding-8"> 
                    <span onclick="document.getElementById('cariDataModal').style.display='none'" class="w3-button w3-hide-large w3-hide-medium w3-light-blue w3-hover-red w3-large w3-display-topleft">&times;</span>
                    <span onclick="document.getElementById('cariDataModal').style.display='none'" class="w3-button w3-hide-small w3-light-blue w3-hover-red w3-large w3-display-topright">&times;</span>
                    <h2 class="w3-large"> <strong>Cari Data Login</strong> </h2>
                </header>

                <div class="w3-container">
                    <br>
                    Nama ? <input class="w3-input" type="text" style="margin-bottom:3px" id="datadicari">
                    <div style="max-height: 300px; overflow-y: auto;">
                        <span id="resdata"> </span>
                    </div>
                    <br>
                </div>
            </div>
            <br><br>
        </div>

        <!-- konfirm data log Modal -->
        <div id="konfirmDataLogModal" class="w3-modal" style="z-index: 5">
            <div class="w3-modal-content w3-animate-top w3-light-grey w3-card-4">
                <header class="w3-container w3-light-blue w3-center w3-padding-8">
                    <span onclick="document.getElementById('konfirmDataLogModal').style.display='none'" class="w3-button w3-hide-large w3-hide-medium w3-light-blue w3-hover-red w3-large w3-display-topleft">&times;</span>
                    <span onclick="document.getElementById('konfirmDataLogModal').style.display='none'" class="w3-button w3-hide-small w3-light-blue w3-hover-red w3-large w3-display-topright">&times;</span>
                    <h2 class="w3-large"> <strong>Cek Login</strong> </h2>
                </header>

                <center>
                    <div class="w3-container">
                    <br>Pastikan data kamu sudah benar !!
                    <div style="height: 10px"></div>

                    <b><span class="dataNama" style="font-weight: bold"></span></b>
                    <br>
                    <span style="color:black">Bid. Studi :</span>
                    <span class="dataMapel" style="font-weight: bold; color:#23A1D3; font-family: Arial"> </span>
                    <br><br>
                    <div id="infoText"></div>
                    <br>
                    </div>
                </center>

                <footer class="w3-container" style="background-color: #D5D5D5">
                    <div style="height: 10px"></div>

                    <center>
                        <form action="cls.php" method="post" target="_parent">
                        <input type="submit" id="btnCancelLogin" class="w3-btn w3-red" value="Salah">
                        &emsp;
                        <button type="button" id="btnGoLogin" class="w3-btn w3-green">Lanjut</button>
                        </form>
                    </center>
                    <div style="height: 10px"></div>
                </footer>
            </div>
            <br><br>
        </div>

        <!-- Sidebar/menu -->
        <div class="w3-sidebar w3-collapse w3-animate-left" style="z-index:4;width:36%;background-color:#E8EEFF;overflow:hidden;" id="mySidebar"><br>
            <div class="w3-container">
                <center>
                    <H2 style="font-size:2vw"><?= $judulBesar; ?></H2>

                    <img style="max-width:100%; height:auto" src="images/logo_sekolah.png?<?= rand(); ?>" alt="<?= $namaSekolahFront; ?>" border="0" />
                    <H3><?= $namaSekolahFront; ?></H3><br>
                    <font color="black"><H4><?= $bentukPelaksanaan; ?></H4></font><br>
                    <img style="max-width:100%; height:auto" src="images/logoNFBIG.png" alt="BKB NF" border="0" />
                    <br><br><br>
                    <span style="font-size:16px;color:#0948B2"><a href="https://bimbelnurulfikri.id" style="text-decoration: none" target="_blank">
                    bimbelnurulfikri.id</a> &copy; <?= $tah[0].$tah[1].$tah[2].$tah[3]; ?>
                    </span>
                </center>
            </div>
        </div>

        <!-- !PAGE CONTENT! -->
        <div id="rightPanel" class="w3-main" style="margin-left:36%;">

            <!-- Top Bar -->
            <div id="topLoginBar" class="w3-bar w3-large" style="z-index:3; background-color: #527FC9; box-shadow: 0px 3px 7px #999;">
                <span class="w3-bar-item w3-left" style="margin-left:22px; font-weight:bold; color: white">Login Peserta</span>
            </div>

            <div class="w3-panel">
                <div class="w3-row-padding" style="margin:0 -16px">
                    <span class="dataNama" style="margin-left:30px;float:left; margin-top:2px; color:#527FCD; font-family:tahoma; font-weight:bold; font-size:14px;"></span>
                    <div id="daftar" style="padding-right:20px;margin-left:30px; margin-top:30px; font-family:'Tahoma'; color:#527FC9">

                        <form action="testpage.php" id="formLogin" method="post" name="logMember" target="_parent">
                            <table>
                                <?php
                                    if ($showSearch == 1) {
                                        echo 
                                        "<tr>
                                        <th class='dataFinder' scope='row' align='left' style='cursor:pointer;'><span style='color:#F8A94E'>Search... </span><img src='images/smallblue_lup.png' style='max-width:100%; height:auto; transform: translateY(-4px);' border='0' /></th>
                                        <td> </td>
                                        </tr>";
                                    }
                                ?>
                                <tr>
                                    <th scope="row" class="respEl" align="left"><span><?= $teksno1; ?></span></th>
                                    <td>
                                        <input id="noID" name="noID" title="<?= $nteks1; ?> karakter" type="text" size="15" maxlength="<?= $nteks1; ?>" value="<?= $prefixid1; ?>" placeholder="<?= $teksno1; ?>">
                                        <img id="check1" style="max-width:100%; height:auto;" src="" border="0" />
                                    </td>
                                </tr>     
                                <tr>
                                    <th scope="row" class="respEl" align="left"><span><?= $teksno2; ?></span></th>
                                    <td>
                                        <input id="noPeserta" name="noPeserta" title="<?= $nteks2; ?> karakter" type="text" size="15" maxlength="<?= $nteks2; ?>" value="<?= $prefixid2; ?>" placeholder="<?= $teksno2; ?>">
                                        <img id="check2" style="max-width:100%; height:auto;" src="" border="0" />&nbsp;
                                        <input type="button" id="thePW" onclick="shpw()" style="cursor: pointer; border-radius: 3px; border: 0px; background-color: transparent; text-align: center; width: 26px; height: 25px;" value="&#128064;">
                                    </td>
                                </tr>

                                <tr id="tempatInputHP">
                                    <th scope="row" class="respEl" align="left">Nomor HP</th>
                                    <td><input class="validate[minSize[10],custom[phone]]" id="noHP" name="noHP" type="text" size="15" maxlength="20" placeholder="Nomor HP"></td>
                                </tr>

                                <tr id="tempatInputEmail">
                                    <th scope="row" class="respEl" align="left">Email</th>
                                    <td><input class="validate[custom[email]]" id="email" name="email" type="email" size="23" placeholder="Email"></td>
                                </tr>

                                <tr id="tempatInputDoB">
                                    <th scope="row" class="respEl" align="left">Tgl. Lahir</th>
                                    <td><input id="getDoB" name="getDoB" type="text" size="10" placeholder="Tgl. Lahir"></td>
                                </tr>

                                <tr id="HSpace1"><td height="10"></td></tr>

                                <tr id="inputPJ1">
                                    <th scope="row" class="respEl" align="left">Kode Pilihan 1 &emsp;</th>
                                    <td><input id="pj1" class="jurSearch" name="pj1" type="text" size="7" placeholder="Kode Pil. 1"></td>
                                </tr>

                                <tr id="inputPJ2">
                                    <th scope="row" class="respEl" align="left">Kode Pilihan 2 &emsp;</th>
                                    <td><input id="pj2" class="jurSearch" name="pj2" type="text" size="7" placeholder="Kode Pil. 2"></td>
                                </tr>

                                <!-- SBMPTN model lama bisa milih sampai 3 pilihan prodi
                                <tr id="inputPJ3">
                                <th scope="row" class="respEl" align="left">Kode Pilihan 3 </th>
                                <td><input id="pj3" class="jurSearch" name="pj3" type="text" size="7" placeholder="Kode Pil. 3"></td>
                                </tr>
                                -->

                                <tr>
                                    <th scope="row" class="respEl" align="left">Bid. Studi</th>
                                    <td>
                                        <select name="pilSet" id="pilSet" style="width: 260px">
                                            <option value="0" selected> - Pilih Bid. Studi - </option>
                                        </select>
                                    </td>
                                </tr>

                            </table>
                            <input type="hidden" id="acakanSoal" val=''>
                            

                            <hr style="border-top: 1px dashed #A8DCF1; border-bottom: 0px;">    <!-- // ini garis putus-putus ornamen login -->

                            
                            <span id="loginLa" style="color:black">Silakan lengkapi data di atas</span>
                            </br>
                            <span id="laTO" style="color:red;"><b>&nbsp;</b></span>
                        </form>

                        <span style="color:black; font-size:1.2vw"><?= "<br><H3>".$motoSekolahFront."</H3>"; ?></span>
                    </div>

                    <!-- text helper, di pojok kanan bawah -->
                    <?php
                        if($adminNo!='' || $linkThisGrup!='') {
                            echo "<div id='txtAdmin' style='right:10px; bottom:10px; position:absolute; font-family:Arial; font-size:15px; border-radius: 4px;
                            padding: 3px 6px 3px 6px; background-color:#EEEEEE; border:2px solid #D0D0D0;'>";

                            if($adminNo!='')
                            { echo "Ada kendala ? hubungi $adminName di <a href='https://wa.me/+62".substr($adminNo,1)."' target='_blank'><span style='color:blue'>$adminNo</span></a><br>"; }

                            if($linkThisGrup!='')
                            { echo "<a href='$linkThisGrup' style='color:green; text-decoration:none' target='_blank'>Klik link ini untuk join grup WA</a>";
                            }

                            echo "</div>";
                        }
                    ?>
                </div>
            </div>

        </div>
        

        <script>
            // Get the Sidebar
            var mySidebar = document.getElementById("mySidebar");

            // Get the DIV with overlay effect
            var overlayBg = document.getElementById("myOverlay");

            var idleTime = <?= $numericTime; ?>;
            var dTimer = <?= $dTimer; ?>;

            var waktuKini = <?= $numericTime; ?>;
            var shiftAktif, jamMulai, mntMulai, endSesi, startLog;
            var patokanSesi, akhirSesi;
            var patokanLogin, mulaiBolehLog, mulaiTO, akhirTO;
            var fLog = kLog = 0;
            var shiftnya = 0;
            var lenLbl1 = '<?= $nteks1; ?>';
            var lenLbl2 = '<?= $nteks2; ?>';
            var pernahFull = false;

            var inp2passw = <?= $inp2pw; ?>;
            if (inp2passw==1) {
                $("#noPeserta").prop('type', 'password');
                $("#thePW").show();
                $("#lblthePW").show();
            }
            else {
                //$("#noPeserta").prop('type', 'text');
                $("#thePW").hide();
                $("#lblthePW").hide();
            }

            var no_nid = '<?= $nid; ?>';
            var no_npd = '<?= $npd; ?>';

            var askHP = <?= $showHP; ?>;
            if (askHP==0)
            { $("#tempatInputHP").hide(); }
            else
            { $("#tempatInputHP").show(); }

            var askEmail = <?= $showEmail; ?>;
            if (askEmail==0)
            { $("#tempatInputEmail").hide(); }
            else
            { $("#tempatInputEmail").show(); }

            var askDoB = <?= $showDoB; ?>;
            if (askDoB==0)
            { $("#tempatInputDoB").hide(); }
            else
            { $("#tempatInputDoB").show(); }

            var askJur = <?= $showJur; ?>;
            if (askJur==0) {
                $("#HSpace1").hide();
                $("#HSpace2").hide();
                $("#inputPJ1").hide();
                $("#inputPJ2").hide();
                //$("#inputPJ3").hide();
            }
            else {
                $("#HSpace1").show();
                $("#HSpace2").show();
                $("#inputPJ1").show();
                $("#inputPJ2").show();
                //$("#inputPJ3").show();
            }

            var delay = (function(){
                var timer = 0;
                return function(callback, ms){
                    clearTimeout(timer);
                    timer = setTimeout(callback,ms);
                };
            })();

            // Toggle between showing and hiding the sidebar, and add overlay effect
            function w3_open() {
                if (mySidebar.style.display === 'block') {
                    mySidebar.style.display = 'none';
                    overlayBg.style.display = "none";
                }
                else {
                    mySidebar.style.display = 'block';
                    overlayBg.style.display = "block";
                }
            }

            // Close the sidebar with the close button
            function w3_close() {
                mySidebar.style.display = "none";
                overlayBg.style.display = "none";
            }

            function refreshTime() {
                $.ajax({
                    type: "post",
                    url: "ajaxjsonRefreshTime.php",
                    cache: false,
                    async: false,         // tanpa ini variable result dari ajax akan menghasilkan undefined
                    dataType: "json",
                    success: function(getResult) {
                        shiftAktif = getResult[0];
                        jamMulai = getResult[1];
                        mntMulai = getResult[2];
                        endSesi = getResult[3];
                        startLog = getResult[4];
                    }
                });
            }

            function cekuser() {
                if (no_nid!='' && no_npd!='') {
                    $("#noID").val(no_nid);
                    $("#noPeserta").val(no_npd);
                }

                var adaNoID = $("#noID").val();
                var adaNoPeserta = $("#noPeserta").val();

                if (adaNoID.length==lenLbl1 && adaNoPeserta.length==lenLbl2) {
                    $.ajax({
                    type: "post",
                    url: "active.php",
                    data: {nIN:adaNoID, nPE:adaNoPeserta},
                    cache: false,
                    dataType: "json",
                    success: function(jsonData) {
                        if (jsonData[0]<2)    //ada error data login
                        { $(".dataNama").html("<span style='color:red;font-weight:bold'>Invalid Data !!</span>"); }
                        else {
                                $("#check1, #check2").attr("src", "images/check-mark.png");

                                if (pernahFull==false) {
                                    $(".dataNama").html(jsonData[1] + " - " + jsonData[2]);
                                    $(".dataFinder").css("visibility", "hidden");

                                    if (jsonData[6].length>0)
                                    { $("#pilSet").append(jsonData[6]); }

                                    if (jsonData[7] != '')
                                    { $("#noHP").val(jsonData[7]); }

                                    if (jsonData[8] != '')
                                    { $("#email").val(jsonData[8]); }

                                    if (jsonData[9] != '')
                                    { $("#getDoB").val(jsonData[9]); }

                                    if (jsonData[10] != '')
                                    { $("#pj1").val(jsonData[10]); }
                                    if (jsonData[11] != '')
                                    { $("#pj2").val(jsonData[11]); }
                                    /*
                                    if (jsonData[12] != '')
                                    { $("#pj3").val(jsonData[12]); }
                                    */

                                    jur = jsonData[3];
                                    if (jur=="IPA") {
                                        $("#kelompok").val('saintek');
                                    }
                                    else if (jur=="IPS") {
                                        $("#kelompok").val('soshum');
                                    }

                                    pernahFull = true;
                                }

                                kLog = jsonData[5];       //loginkey
                                fLog = jsonData[14];      //loginflag
                                shiftnya = jsonData[13];
                            }
                        }
                    });
                }
                else {
                    pernahFull = false;
                    $(".dataNama").html("");
                    $("#pilSet").html("<option value='0' selected> - Pilih Bid. Studi - </option>");
                    $("#check1, #check2").attr("src", "");

                    $("#noHP").val('');
                    $("#email").val('');
                    $("#getDoB").val('');

                    $("#pj1").val('');
                    $("#pj2").val('');
                    //$("#pj3").val('');
                }
            }

            function ceklog() {
                patokanSesi = (jamMulai*3600)+(mntMulai*60);
                akhirSesi = patokanSesi+(endSesi*60);

                if (shiftAktif==0) 
                { patokanLogin = 86399; } //Tdk ada Shift Aktif ?, TUNGGULAH SAMPE JAM 23:59:59 mlm atau silakan bagi yg di-logkey !!
                else {
                    patokanLogin = patokanSesi-(startLog*60);

                    if (waktuKini >= akhirSesi) {
                        patokanLogin = 86399;
                        patokanSesi = 86399;
                    }
                }

                var mulaiBolehLog = patokanLogin;
                var mulaiTO = patokanSesi;
                var akhirTO = akhirSesi;

                waktuKini += 3;
                if (waktuKini >= mulaiBolehLog && waktuKini < mulaiTO && shiftAktif==shiftnya) {
                    if ($("#pilSet").val()!="0" && (askJur==0 || ($("#pj1").val()!="" || $("#pj2").val()!="")))
                    { $("#loginLa").html('Silakan lengkapi data di atas, sudah boleh <input class="btnLogin" name="login" type="button" style="cursor:pointer" value="&nbsp; Login&nbsp; ">'); }
                    else
                    { $("#loginLa").html('Silakan lengkapi data di atas'); }
                }
                else if ((waktuKini >= mulaiTO && waktuKini < akhirTO && shiftAktif==shiftnya && fLog==0) || kLog!=0) {
                    if ($("#pilSet").val()!="0" && (askJur==0 || ($("#pj1").val()!="" || $("#pj2").val()!="")))
                    { $("#loginLa").html('Silakan klik <input class="btnLogin" name="login" type="button" style="cursor:pointer" value="&nbsp; Login&nbsp; ">'); }
                    else
                    { $("#loginLa").html('Segera lengkapi data di atas'); }

                    $("#laTO").html('<b>Ujian telah berjalan !!</b>');
                }
                else {
                    $("#loginLa").html('Silakan lengkapi data di atas');
                    $("#laTO").html('');
                }
            }

            $(window).load(function() {

                refreshTime();
                cekuser();
                setInterval(ceklog, 500);

                $(".dataFinder").click(function() {
                    $("#cariDataModal").css("display", "block");
                    $("#resdata").html('');
                    $("#datadicari").focus();
                });


                $("#datadicari").keyup(function() {
                    delay(function() {
                        if ($("#datadicari").val().length>2) {
                            $.ajax({
                                type: "post",
                                url: "ajaxCariData.php",
                                data: {kData:$("#datadicari").val()},
                                cache: false,
                                success: function(gotIt)
                                    { $("#resdata").html(gotIt); }
                            });
                        }
                        else
                        { $("#resdata").html(''); }
                    }, 300);
                });

                $(document).on("click", ".hasilData", function() {
                    itsData = $(this).attr('tagData');

                    $("#datadicari").val('');
                    $("#cariDataModal").css("display", "none");
                    $(".dataFinder").css("visibility", "hidden");

                    dataLogin = itsData.split("#");
                    $("#noID").val(dataLogin[0]);
                    $("#noPeserta").val(dataLogin[1]);

                    refreshTime();
                    cekuser();
                });

                $("#noID, #noPeserta").keyup(function() {
                    delay(function() {
                        if ($("#noID").val().length==lenLbl1 && $("#noPeserta").val().length==lenLbl2) {
                            refreshTime();
                            cekuser();
                        }
                    }, 300);
                });


                $("#namaptn").keyup(function() {
                    $("#resnamajur").html('');
                    delay(function() {
                        toFind = "ptn";
                        nowklp = $("#kelompok").find(":selected").val();
                        nowjnj = $("#jenjang").find(":selected").val();
                        if ($("#namaptn").val().length>2) {
                            $.ajax({
                                type: "post",
                                url: "ajaxCariKodeJurusan.php",
                                data: {tF: toFind, clue1:nowklp, clue2:nowjnj, clue3:$("#namaptn").val(), clue4:''},
                                cache: false,
                                success: function(getIt)
                                { $("#resnamaptn").html(getIt); }
                            });
                        }
                        else {
                            $("#resnamaptn").html('');
                            $("#namajur").val('');
                            $("#resnamajur").html('');
                        }
                    }, 300);
                });

                $(document).on("click", ".listSearchPTN", function() {
                    nmData = $(this).attr('tagnm');
                    $("#namaptn").val(nmData);
                    $("#resnamaptn").html('');
                    $("#namajur").val('');
                    $("#namajur").trigger('keyup');
                });

                $("#namajur").on("focus keyup", function() {
                    delay(function() {
                        toFind = "prodi";
                        nowklp = $("#kelompok").find(":selected").val();
                        nowjnj = $("#jenjang").find(":selected").val();
                        if ($("#namaptn").val().length>2) {
                                $.ajax({
                                type: "post",
                                url: "ajaxCariKodeJurusan.php",
                                data: {tF: toFind, clue1:nowklp, clue2:nowjnj, clue3:$("#namaptn").val(), clue4:$("#namajur").val()},
                                cache: false,
                                success: function(getIt)
                                { $("#resnamajur").html(getIt); }
                            });
                        }
                        else
                        { $("#resnamajur").html(''); }
                    }, 300);
                });

                $(document).on("click", ".listSearchJur", function() {
                    nmData = $(this).attr('tagnm');
                    kodenya = $(this).attr('tagkd');
                    $("#namajur").val(nmData);
                    $("#kodejurnya").val(kodenya);
                    $("#resnamajur").html('');
                });

                $("#btnGetCode").click(function() {
                    if ($("#untukpj").val()=='pj1')
                    { $("#pj1").val($("#kodejurnya").val()); }
                    else if ($("#untukpj").val()=='pj2')
                    { $("#pj2").val($("#kodejurnya").val()); }
                    /*
                    else if ($("#untukpj").val()=='pj3')
                    { $("#pj3").val($("#kodejurnya").val()); }
                    */
                    $("#piljurModal").css("display", "none");
                });

                $(".jurSearch").click(function() {
                    if ($(this).attr("id")=='pj1') {
                        $("#untukpj").val('pj1');
                        kodeAda = $("#pj1").val();
                    }
                    else if ($(this).attr("id")=='pj2') {
                        $("#untukpj").val('pj2');
                        kodeAda = $("#pj2").val();
                    }
                    /*
                    else {
                        $("#untukpj").val('pj3');
                        kodeAda = $("#pj3").val();
                    }
                    */

                    if (kodeAda!='') {
                        $.ajax({
                            type: "post",
                            url: "ajaxjsonCariJurusan.php",
                            data: {kA:kodeAda},
                            cache: false,
                            dataType: "json",
                            success: function(getProdi) {
                                grup = getProdi[0];
                                ptn = getProdi[1];
                                jur = getProdi[2];
                                jnj = jur.substr(-3, 2);
                                if (grup=='SAINTEK') { $("#kelompok").val('saintek'); } else if (grup=='SOSHUM') { $("#kelompok").val('soshum'); }
                                if (jnj=='S1') { $("#jenjang").val('s1'); } else if (jnj=='D4') { $("#jenjang").val('d4'); }
                                $("#namaptn").val(ptn);
                                $("#namajur").val(jur);
                            }
                        });
                    }
                    else {
                        $("#namaptn").val('');
                        $("#namajur").val('');
                    }
                    $("#piljurModal").css("display", "block");
                });

                $("#formLogin").validationEngine({
                    promptPosition: "topLeft:35,13",
                    scroll: false
                });

                $("#kelompok, #jenjang").change(function() {
                    $("#namaptn").val('');
                    $("#namajur").val('');
                });

                $("#pilSet").change(function() {
                    pilSetVal = $(this).val();
                    $("#acakanSoal").val('');

                    if (pilSetVal!=0) {
                        $(".dataMapel").text($("#pilSet option:selected").text());
                        adaNoID = $("#noID").val();
                        adaNoPeserta = $("#noPeserta").val();
                        // cek keberadaan acakan soalnya di tabel absensiharitespeserta
                        $.ajax({
                            type: "post",
                            url: "ajaxjsonCekAcakanSoal.php",
                            data: {nIN:adaNoID, nPE:adaNoPeserta, pSet:pilSetVal},
                            cache: false,
                            dataType: "json",
                            success: function(cekSoal) {
                                itemPerBS = cekSoal[0];
                                totS = cekSoal[1];
                                fSoal = cekSoal[2];
                                pathSoal = cekSoal[3];
                                if (itemPerBS!='') {
                                    acakNoSoal = acakinSoal(itemPerBS, totS, fSoal);
                                    perImg = acakNoSoal.split(",");
                                    dfile = "images/soal/"+pathSoal+"/"+perImg[0]+".png";
                                    imageObject = new Image();
                                    imageObject.src = dfile;
                                }
                                else
                                { acakNoSoal = cekSoal[1]; }

                                $("#acakanSoal").val(acakNoSoal);
                            }
                        });
                    }
                });

                $(document).on("click", ".btnLogin", function()
                { $("#konfirmDataLogModal").css("display", "block"); });

                $("#btnGoLogin").mousedown(function() {
                    $("#infoText").html("Menyiapkan soal ... " + "<img src='images/open-book-ani.gif' border='0' />");

                    pilSetVal = $("#pilSet").val();
                    setPil = pilSetVal.split("-");
                    idxH = setPil[1];
                    adaNoID = $("#noID").val();
                    adaNoPeserta = $("#noPeserta").val();
                    soalAcak = $("#acakanSoal").val();

                    if (dTimer > idleTime || dTimer==0 || (no_nid!='' && no_npd!='')) {
                        if (soalAcak!='') {
                            $.ajax({
                                type: "post",
                                url: "saveAcakSoal.php",
                                data: {hk:idxH, ni:adaNoID, np:adaNoPeserta, ss:soalAcak},
                                cache: false,
                                success: function(cekAcak) {
                                    if (cekAcak!="")
                                    { $("#formLogin").submit(); }
                                    else {
                                        $("#infoText").html("");
                                        $("#konfirmDataLogModal").css("display", "none");
                                    }
                                }
                            });
                        }
                        else {   // ternyata acakanSoalnya kosong -> acak dulu
                            $.ajax({
                                type: "post",
                                url: "ajaxjsonCekAcakanSoal.php",
                                data: {nIN:adaNoID, nPE:adaNoPeserta, pSet:pilSetVal},
                                cache: false,
                                dataType: "json",
                                success: function(cekSoal) {
                                    itemPerBS = cekSoal[0];
                                    totS = cekSoal[1];
                                    fSoal = cekSoal[2];
                                    pathSoal = cekSoal[3];
                                    if (itemPerBS!='') {
                                        acakNoSoal = acakinSoal(itemPerBS, totS, fSoal);
                                        perImg = acakNoSoal.split(",");
                                        dfile = "images/soal/"+pathSoal+"/"+perImg[0]+".png";
                                        imageObject = new Image();
                                        imageObject.src = dfile;
                                    }
                                    else
                                    { acakNoSoal = cekSoal[1]; }

                                    $("#acakanSoal").val(acakNoSoal);

                                    $("#infoText").html("");
                                    $("#konfirmDataLogModal").css("display", "none");
                                }
                            });
                        }
                    }
                });
            });

            //Cegah backward from browser
            function preventBack(){window.history.forward();}
            setTimeout("preventBack()", 0);
            window.onunload = function(){null};
        </script>

    </body>
</html>