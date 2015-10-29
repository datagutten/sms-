<?php
//Exension to the PSWinCom class to add an easy function to a single message to multiple recipients
require 'PSWinCom-sms-class/class.php';
class pswinsms_extend extends pswinsms
{
	public $sender;
	public function __construct($username,$password,$sender)
	{
		parent::__construct($username,$password);
		$this->sender=$sender;
	}
	public function sendmessage($to,$text)
	{
		if(is_array($to))
		{
			foreach($to as $recipient)
				parent::addmessage($recipient,$text,$this->sender);
			return parent::sendmessages();
		}
		else
			return parent::sendsinglemessage($to,$text,$this->sender);
	}
	
}