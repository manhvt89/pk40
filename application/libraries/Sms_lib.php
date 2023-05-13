<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class Sms_lib
{
	private $CI;
	public $port = 'COM3';
	public $baud = 115200;

	public $debug = false;

	private $fp;
	private $buffer;

  	public function __construct()
	{
		$this->CI =& get_instance();
		//$this->init();
	}

	//Setup COM port
	public function init() {

		$this->debugmsg("Setting up port: \"{$this->port} @ \"{$this->baud}\" baud");

		exec("MODE {$this->port}: BAUD={$this->baud} PARITY=N DATA=8 STOP=1", $output, $retval);

		if ($retval != 0) {
			//throw new Exception('Unable to setup COM port, check it is correct');
			$this->debugmsg("Setting up COM port fails");
			return false;
		}

		$this->debugmsg(implode("\n", $output));

		$this->debugmsg("Opening port");

		//Open COM port
		$this->fp = fopen($this->port . ':', 'r+');

		//Check port opened
		if (!$this->fp) {
			//throw new Exception("Unable to open port \"{$this->port}\"");
			return false;
		}

		$this->debugmsg("Port opened");
		$this->debugmsg("Checking for responce from modem");

		//Check modem connected
		fputs($this->fp, "AT\r");

		//Wait for ok
		$status = $this->wait_reply("OK\r\n", 5);

		if (!$status) {
			//throw new Exception('Did not receive responce from modem');
			return false;
		}

		$this->debugmsg('Modem connected');

		//Set modem to SMS text mode
		$this->debugmsg('Setting text mode');
		fputs($this->fp, "AT+CMGF=1\r");

		$status = $this->wait_reply("OK\r\n", 5);

		if (!$status) {
			//throw new Exception('Unable to set text mode');
			return false;
		}

		$this->debugmsg('Text mode set');
		return true;

	}

	//Wait for reply from modem
	private function wait_reply($expected_result, $timeout) {

		$this->debugmsg("Waiting {$timeout} seconds for expected result");

		//Clear buffer
		$this->buffer = '';

		//Set timeout
		$timeoutat = time() + $timeout;

		//Loop until timeout reached (or expected result found)
		do {

			$this->debugmsg('Now: ' . time() . ", Timeout at: {$timeoutat}");

			$buffer = fread($this->fp, 1024);
			$this->buffer .= $buffer;

			usleep(200000);//0.2 sec

			$this->debugmsg("Received: {$buffer}");

			//Check if received expected responce
			if (preg_match('/'.preg_quote($expected_result, '/').'$/', $this->buffer)) {
				$this->debugmsg('Found match');
				return true;
				//break;
			} else if (preg_match('/\+CMS ERROR\:\ \d{1,3}\r\n$/', $this->buffer)) {
				return false;
			}

		} while ($timeoutat > time());

		$this->debugmsg('Timed out');

		return false;

	}

	//Print debug messages
	private function debugmsg($message) {

		if ($this->debug == true) {
			$message = preg_replace("%[^\040-\176\n\t]%", '', $message);
			echo $message . "\n";
		}

	}

	//Close port
	public function close() {

		$this->debugmsg('Closing port');

		fclose($this->fp);

	}

	//Send message
	public function send($tel, $message) {

		//Filter tel
		$tel = preg_replace("%[^0-9\+]%", '', $tel);

		//Filter message text
		$message = preg_replace("%[^\040-\176\r\n\t]%", '', $message);

		$this->debugmsg("Sending message \"{$message}\" to \"{$tel}\"");

		//Start sending of message
		fputs($this->fp, "AT+CMGS=\"{$tel}\"\r");

		//Wait for confirmation
		$status = $this->wait_reply("\r\n> ", 5);

		if (!$status) {
			//throw new Exception('Did not receive confirmation from modem');
			$this->debugmsg('Did not receive confirmation from modem');
			return false;
		}

		//Send message text
		fputs($this->fp, $message);

		//Send message finished indicator
		fputs($this->fp, chr(26));

		//Wait for confirmation
		$status = $this->wait_reply("OK\r\n", 180);

		if (!$status) {
			//throw new Exception('Did not receive confirmation of messgage sent');
			$this->debugmsg('Did not receive confirmation of messgage sent');
			return false;
		}

		$this->debugmsg("Message sent");

		return true;

	}
	/*
	 * SMS sending function
	 * Example of use: $response = sendSMS('4477777777', 'My test message');
	 */
	public function sendSMS($phone, $message)
	{
		$username   = $this->CI->config->item('msg_uid');
		$password   = $this->CI->encryption->decrypt($this->CI->config->item('msg_pwd'));
		$originator = $this->CI->config->item('msg_src');
		
		$response = FALSE;
		
		// if any of the parameters is empty return with a FALSE
		if(empty($username) || empty($password) || empty($phone) || empty($message) || empty($originator))
		{
			//echo $username . ' ' . $password . ' ' . $phone . ' ' . $message . ' ' . $originator;
		}
		else
		{	
			$response = TRUE;
			
			// make sure passed string is url encoded
			$message = rawurlencode($message);
			
			// add call to send a message via 3rd party API here
			// Some examples

			/*
			$url = "http://xxx.xxx.xxx.xxx/send_sms?username=$username&password=$password&src=$originator&dst=$phone&msg=$message&dr=1";
			 
			$c = curl_init(); 
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($c, CURLOPT_URL, $url); 
			$response = curl_exec($c); 
			curl_close($c);
			*/
			
			// This is a textmarketer.co.uk API call, see: http://wiki.textmarketer.co.uk/display/DevDoc/Text+Marketer+Developer+Documentation+-+Wiki+Home
			/*
			$url = 'https://api.textmarketer.co.uk/gateway/'."?username=$username&password=$password&option=xml";
			$url .= "&to=$phone&message=".urlencode($message).'&orig='.urlencode($originator);
			$fp = fopen($url, 'r');
			$response = fread($fp, 1024);
			*/
		}

		return $response;
	}
}

?>
