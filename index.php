<?php
if(!empty($_POST['sms_user']) && !empty($_POST['sms_password']))
{
	if(!isset($_POST['sms_provider']))
		$_POST['sms_provider']='account';
	foreach($_POST as $key=>$value) //Save login as a cookie for 365 days
	{
		if(substr($key,0,3)=='sms')
		{
			setcookie($key,$value,strtotime('+365 days'));
			$_COOKIE[$key]=$value;
		}
	}
}
if(isset($_POST['logoff'])) //Remove cookies
{
	foreach($_COOKIE as $key=>$value)
		if(substr($key,0,3)=='sms')
		{
			unset($_COOKIE[$key]);
			setcookie($key,'',time()-3600);
		}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>SMS</title>
<script src="sms.js" type="text/javascript"></script>
<link href="sms.css" rel="stylesheet" type="text/css">
</head>

<body onload="resize()" onResize="buttonwidth()">
<div id="content">
<?Php
require 'PSWinCom-sms-class-extend.php';
if(file_exists('config.php'))
	require 'config.php';
require 'telenor_sms.php';

if(!empty($_COOKIE['sms_provider']) && !empty($_COOKIE['sms_user']) && !empty($_COOKIE['sms_password'])) //Load login from cookies
{
	if($_COOKIE['sms_provider']=='account')
	{
		$user=strtolower($_COOKIE['sms_user']);
		if(isset($config['accounts'][$user]))
		{
			if($_POST['sms_password']!=$config['accounts'][$user]['password'])
				echo "Feil passord";
			elseif($config['accounts'][$user]['provider']=='linkmobility')
				$sms=new pswinsms_extend($config['providers']['linkmobility']['username'],$config['providers']['linkmobility']['password'],$config['accounts'][$user]['sender']);
			else
				echo "Feil i config, ukjent leverandør";
		}
		else
			echo "Ukjent bruker: ".$user;
	}
	elseif($_COOKIE['sms_provider']=='telenor')
		$sms=new telenor_sms($_COOKIE['sms_user'],$_COOKIE['sms_password']);
	elseif($_COOKIE['sms_provider']=='pswincom')
		$sms=new pswinsms_extend($_COOKIE['sms_user'],$_COOKIE['sms_password'],$_COOKIE['sms_sender']);
	else
		trigger_error("Invalid provider: {$_COOKIE['sms_provider']}",E_USER_ERROR);
}
if(!empty($_POST['to']) && !empty($_POST['message']))
{
	if(!isset($sms))
		trigger_error("SMS class not loaded",E_USER_ERROR);
	if(strpos($_POST['to'],',')!==false)
		$recipients=explode(",",$_POST['to']);
	else
		$recipients=array($_POST['to']);
	foreach($recipients as $key=>$recipient)
	{
		if(strlen($recipient)==8)
			$recipients[$key]='47'.$recipient; //Add country code
	}
	$status=$sms->sendmessage($recipients,$_POST['message']);

	if(is_string($status))
		$string=$status;
	else
	{
		$string="Meldingen ble sendt til følgende mottakere:\\n";
		foreach($status as $recepient=>$recipient_status)
		{
			$string.=$recepient.': ';
			if($recipient_status===true)
				$string.='OK';
			else
				$string.='Feil: '.$recipient_status;
			$string.="\\n";
		}
	}
    echo "<script type=\"text/javascript\">alert('$string')</script>\n";
}

?>
<?php
if(isset($sms))
{
?>
<div id="send">
<form id="form" name="form" method="post">
	<p>
		<input name="to" type="text" id="to" placeholder="Til">
	</p>
	<p><textarea name="message" id="message" placeholder="Skriv inn din melding" oninput="count_chars()"></textarea></p>
	<input type="submit" value="Send" name="send" class="halfwidth" id="button_send"><input type="submit" value="Logg av" name="logoff" class="halfwidth" id="button_logoff">
	<p><span id="charcount" style="display:none"></span></p>
</form>
</div>
<?php
}
else
{
?>
<div id="login">
  <form id="login_form" name="login_form" method="post">
	<p>
		<input name="sms_user" type="text" id="sms_user" placeholder="Brukernavn">
	</p>
    <p>
    <input name="sms_password" type="password" id="sms_password" placeholder="Passord" ></p>
<?Php
if(!isset($config['accountonly']))
{
?>
    <p><select name="sms_provider" id="sms_provider" onChange="providerfield()">

  <option value="">Velg leverandør</option>
  <option value="telenor">Telenor</option>
  <option value="pswincom">PSWinCom</option>
  <?Php if(isset($config['accounts']))
  echo '<option value="account">Lokal konto</option>';?>
</select></p><?Php
}
else
{
	?><input type="hidden" name="sms_provider" value="account" /><?php
}
?>
	<p id="provider_fields"></p>
	<input type="submit" value="Logg på">
  </form>

</div>
<?php

}
?>

</div>
</body>
</html>