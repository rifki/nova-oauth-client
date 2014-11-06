<?php
if (!function_exists('curl_init')) { throw new Exception('GOM Account needs the CURL PHP extension.'); }
if (!function_exists('json_decode')) { throw new Exception('GOM Account needs the JSON PHP extension.'); }

/**
 * GOM Account Oauth2 Client
 * @author Muhamad Rifki
 * @version 1.0.0
 */
class GramediaAccountClient {
	const VERSION 				= '1.0.0';
	protected $useragent 		= 'gom-account-oauth-1.0';
	protected $connecttimeout 	= 30;
	protected $timeout 			= 30;
	protected $ssl_verifypeer 	= false; 
	protected $host 			= 'http://localhost/projects/gom-server-sdk'; // URL Host Server

   /**
	* Set API URLs
	*/
	public function authorizeURL()		 { return $this->host.'/oauth/authorize'; }
	public function tokenURL() 			 { return $this->host.'/oauth/access_token'; }
	public function resourceUserURL()	 { return $this->host.'/user'; }
	public function resourceEntriesURL() { return $this->host.'/email'; }

   /**
	* Makes an HTTP request.
	*
	* @param string $url The URL to make the request to
	* @param array $params The parameters to use for the POST body
	* @param headers
	*
	* @return string The response text
	*/
	public function apiRequest($url, $params=false, $headers=array()) 
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
	    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);

		if ($params)
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

		$headers[] = 'Accept: application/json';

		if ($this->session('access_token'))
		$headers[] = 'Authorization: Bearer ' . $this->session('access_token');

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response);
	}
 
	public function get($key, $default=null) 
	{
		return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
	}
 
	public function session($key, $default=null) 
	{
		return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
	}

	public function getState() 
	{ 
		return hash('sha256', microtime(true).rand());
	}	
}

/**
 * Helper for Iframe and Javascript popup fancybox
 */
class Helper {
   /**
	* Fancybox popup
	*
	* @param bool $is_local The asset fancybox
	* @param string $class_iframe name of class iframe (for event javascript default gom_iframe)
	*
	* @return string
	*/
	public static function showPopup($is_local=true) 
	{
		$js = "
			<script type='text/javascript'>
			    $(function() {
			        $('.gom_oauth_iframe').fancybox({
			            type: 'iframe',
			            autoDimensions: false,
			            width: 400,
			            height:400,
			            overlayShow: true,
			            speedIn: 500, 
			            speedOut: 500,
			            transitionIn: 'elastic',
			            transitionOut: 'elastic',
			            afterLoad: function() {
			                //$('#gom_iframe').text('Please wait...');
			            },
			            afterClose: function () {
			                location = document.URL;
			            }
			        });
			    });
			</script>
		";
		return $js;
	}

   /**
	* Display link iframe
	*
	* @param string $class_iframe name of class iframe (default gom_iframe)
	* @param string callback
	*
	* @return string
	*/
	public static function showLink($callback)
	{
		if (!empty($callback)) {
			return "<a class='gom_oauth_iframe' href='".$callback."'>Gramedia Account Connect</a>";	
		} else {
			return false;
		}
	}
}