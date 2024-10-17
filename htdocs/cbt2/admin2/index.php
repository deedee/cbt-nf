<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login Admin 2 - CBT NF</title>
<link href="../images/nf-favico.ico" rel="shortcut icon" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<script type="text/javascript" src="../jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../jquery-ui-1.8.10.custom.min-ubahformattglIndo.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery-ui-1.8.10.custom.css" />
<link rel="stylesheet" type="text/css" href="../jquery.ui.theme.css" />

<style type="text/css">
<!--

body {
    margin: auto;
    border: 0px solid #73AD21;
	background-color: #e8e8e8;
	font-size:21px;
	font-weight:bold;
	color:#006699;
}

.inputBox {
		border-style:solid;
		border-color:#069;
		border-width:4px;
		border-radius:6px;
		font-size:20px;
		font-weight:bold;
		color:#006699;
		text-align:left;
		padding: 6px 10px;
		margin: 8px 0;
		box-sizing: border-box;
}

-->
</style>

<script type="text/javascript">

// Attach the event keypress to enter key
$(document).keypress(function(k) {
	if (k.keyCode == 13){
		$("#bluebutton").trigger('click');
	}
});

$(document).ready(function() {
			
			$("#usern").focus();

			$("#bluebutton").click(function () {
				$("#LogIn").submit();
			});
				
	});

</script>

</head>

<body>
<br>

<div style="width: 700px; margin:auto; background-color:#FAFAFA; padding:25px;">
	<div style="width: 750px; height: 10px; margin:auto; background-color:#0648B7; margin-top:-25px; margin-left:-25px;"></div>

    <br>
        <center>
        
            <img src="../images/logoNFBIG.png" alt="BKB NURUL FIKRI">
            <br><hr width="98%">
            <h3><span style="color:#0948B2">LOGIN ADMIN 2 TO CBT</span></h3>
        
        <span style="font-family:Tahoma, Geneva, sans-serif; font-size:18px;"><b></b></span>
        <p>
        
        <form action="insideindex2.php" method="post" enctype="multipart/form-data" id="LogIn" name="LogIn" target="_parent">
        <table width="600px" border="0" style="margin-left:100px; border-collapse: collapse; font-family:Verdana, Geneva, sans-serif">
          <tr>
            <td width="20">Username</td>
            <td width="300"> : <input name="usern" id="usern" class="inputBox" size="10"></td>
          </tr>
          <tr>
            <td width="20">Password</td>
            <td width="300"> : <input name="passw" id="passw" type="password" class="inputBox" size="23" maxlength="20"></td>
          </tr>
        </table>
        <input name="fromForm" id="fromFrom" type="hidden" value="inForm2">
        </form>
    
        </p>
    
        <div style="width:220px; height:30px; background:#069; padding:10px; text-align:center; margin:auto;
        font-family:Tahoma, Geneva, sans-serif; font-size:20px; font-weight:bold; color:#FFF; cursor:pointer; border-radius:9px;"
        id="bluebutton">Login Admin 2 &nbsp; >></div>
        <br><br>
    
        <div style="width: 750px; height: 2px; margin:auto; background-color:#0648B7; margin-left:-25px;"></div>
        <br>
        <div style="width: 750px; height: 5px; margin:auto; background-color:#0648B7; margin-top:-22px; margin-bottom:-25px; margin-left:-25px;">
    
        </center>
    
	</div>

</div>

</body>
</html>