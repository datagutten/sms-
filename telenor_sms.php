<?Php
//Send SMS using the API used by telenor SMS plugin for outlook
class telenor_sms
{
	private $ch;
	private $username;
	private $password;
	function __construct($username,$password)
	{
		$this->ch=curl_init();
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->ch,CURLOPT_POST,true);
		$this->username=$username;
		$this->password=$password;
	}
	public function sendmessage($to,$text)
	{
		if(is_array($to))
			$to=implode(';',$to);

		$sId='4248523569'; //Used in the request to telenor, don't know what this is, but it don't seem to be changing with different numbers or installs
		$text=urlencode(utf8_decode($text)); //Telenor want the request in ANSI and urlencoded for the POST request.
		$url="https://telenormobil.no/smapi/3/sms?sender=".$this->username."&password=".$this->password."&sId=$sId&recipients=$to&content=$text&responseContentType=text/xml";
		curl_setopt($this->ch,CURLOPT_URL,$url);
		$result=curl_exec($this->ch);
		$xml=simplexml_load_string($result);
		if(!empty($xml->RESPONSE->ERROR))
			return (string)$xml->RESPONSE->ERROR;
		foreach($xml->RESPONSE->RECIPIENT as $recipient_raw)
		{
			$recipient=(array)$recipient_raw;
			$recipient=$recipient['@attributes'];
			if($recipient['status']=='OK')
				$status[$recipient['address']]=true;
			else
			{
				$status[$recipient['address']]=false;
			}
		}
		return $status;
	}
}