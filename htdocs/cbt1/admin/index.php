<?php
    session_start();
?>
<!DOCTYPE html>
<!-- Created By CodingLab - www.codinglabweb.com -->
<html lang="id">
  
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Admin - CBT NF</title>
        <link href="../images/nf-favico.ico" rel="shortcut icon" />

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins',sans-serif;
            }

            body {
                background: #E3EBF2;
                overflow: hidden;
            }

            ::selection {
                background: rgba(124,156,209,0.3);
            }

            .container {
                max-width: 440px;
                padding: 0 20px;
                margin: 170px auto;
                margin-top: 18px;
            }

            .wrapper {
                width: 100%;
                background: #fff;
                border-radius: 5px;
                box-shadow: 0px 4px 10px 1px rgba(0,0,0,0.1);
            }

            .wrapper .toptitle {
                height: 70px;
                background: #567FC5;
                border-radius: 5px 5px 0 0;
                color: #fff;
                font-size: 30px;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .wrapper form {
                padding: 30px 25px 25px 25px;
            }

            .wrapper form .row {
                height: 45px;
                margin-bottom: 15px;
                position: relative;
            }

            .wrapper form .row input {
                height: 100%;
                width: 100%;
                outline: none;
                padding-left: 10px;
                border-radius: 5px;
                border: 1px solid #ADD4FC;
                font-size: 16px;
                transition: all 0.3s ease;
            }

            form .row input:focus {
                border-color: #567FC5;
                box-shadow: inset 0px 0px 2px 2px rgba(86,127,197,0.25);
            }

            form .row input::placeholder {
                color: #999;
            }

            .wrapper form .row i {
                position: absolute;
                width: 47px;
                height: 100%;
                color: #fff;
                font-size: 18px;
                background: #567FC5;
                border: 1px solid #567FC5;
                border-radius: 5px 0 0 5px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .wrapper form .button input {
                color: #fff;
                font-size: 20px;
                font-weight: 500;
                padding-left: 0px;
                background: #567FC5;
                border: 1px solid #567FC5;
                cursor: pointer;
            }

            form .button input:hover {
                background: #355A99;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div style="height: 10px"></div>
            <img src="../images/logoNFBIG.png" alt="BKB NURUL FIKRI" style="max-width: 100%; height: auto;">
            <div style="height: 10px"></div>

            <div class="wrapper">
                <div class="toptitle"><span>Admin CBT NF</span></div>
                <form action="insideindex.php" method="post" id="LogIn" name="LogIn" target="_parent">

                    <div class="row">
                        <!-- <i class="fas fa-user"></i> -->
                        <input type="text" placeholder="Username" name="usern" id="usern" required>
                    </div>
                    
                    <div class="row">
                        <!-- <i class="fas fa-lock"></i> -->
                        <input type="password" placeholder="Password" name="passw" required>
                    </div>
                    <div style="height: 5px"></div>
                    <center>
                        <div class="row">
                            <input type="text" placeholder="captcha" id="txtCaptcha" name="txtCaptcha" maxlength="5" style="width: 142px; font-size: 1em; padding: 7px; text-align: center;" />
                        </div>
                        <img src="captcha.php?rand=<?=rand();?>" id="captcha_image" width="140" />
                        <div style="height:2px"></div>
                        <a href='javascript:ulangCaptcha();' style="font-size: 14px; color:#5a7fc1; transform: translateY(-9px);">New Captcha</a>
                    </center>

                    <br>
                    <div class="row button">
                        <input type="submit" value="Login">
                    </div>

                </form>
            </div>

        </div>

        <script type="text/javascript">
            function ulangCaptcha() {
                const img = document.images['captcha_image'];
                img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random() * 1000;
            }

            window.onload = function() {
                document.getElementById("usern").focus();
            }
        </script>

    </body>
</html>
