<?php
if (!function_exists('curl_init')) { throw new Exception('GramediaAccountClient needs the CURL PHP extension.'); }
if (!function_exists('json_decode')) { throw new Exception('GramediaAccountClient needs the JSON PHP extension.'); }

/**
* GOM Account Oauth2 Client
*
* @author Muhamad Rifki
* @copyright Digital Media - Kompas Gramedia Group of Magazine
* @version 1.0.0
*/
class GramediaAccountClient 
{
	# current version 
	const VERSION = '1.0.0';

	# user agent
	protected $useragent = 'gom-account-oauth-1.0';

	# connection timeout
	protected $connecttimeout = 30;

	# timeout
	protected $timeout = 30;

	# ssl verifypeer
	protected $sslVerifypeer = false; 

	# URL Host Server
	protected $server;

	# client ID
	protected $clientId;

	# client secret
	protected $clientSecret;

	# callback
	protected $callback;

	# appsource website
	protected $appsourceWebsite;

	# appsource system/framework
	protected $appsourceSystem;

	# appsource ip address
	protected $appsourceIpAddress;

	/**
	* Initialize a Gramedia Account Application.
	*
	* The configuration (Mandatory):
	* 	- server: the server host
	* 	- clientId: the client id
	* 	- clientSecret: the client secret
	* 	- callback: the callback URL
	* 	- appsource: the system/framework source
	*
	* @param array $config The application configuration
	*/
	public function __construct($config) {
		if (function_exists('session_status') && 
	    	session_status() !== PHP_SESSION_ACTIVE || 
	    	!session_id()) {
	    	session_start();
	    }

	    $this->setServer($config['server']);
	    $this->setClientId($config['clientId']);
	    $this->setClientSecret($config['clientSecret']);
	    $this->setCallback($config['callback']);
	    $this->setAppsource($config['appsourceWebsite'], $config['appsourceSystem'], $config['appsourceIpAddress']);
	}

	/**
	* Get the getResponseType.
	*
	* @return string type of response
	*/
	public function getResponseType() {
		return 'code';
	}

   /**
	* Set API URLs
	*/
	public function authorizeURL() { 
		return $this->getServer().'/oauth/authorize'; 
	}
	
	/**
	* Get the Token URL.
	*
	* @return string 
	*/
	public function tokenURL() { 
		return $this->getServer().'/oauth/access_token'; 
	}
	
	/**
	* Get the Resource User URL.
	*
	* @return string 
	*/
	public function resourceMembership() { 
		return $this->getServer().'/membership'; 
	}

   /**
	* Makes an HTTP request.
	*
	* @param string $url The URL to make the request to
	* @param array $post_params The parameters to use for the POST body
	* @param headers
	*
	* @return string The response text
	*/
	public function apiRequest($url, $post_params=false, $headers=array()) 
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
	    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer);

		if ($post_params)
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_params));

		$headers[] = 'Accept: application/json';

		//if ($this->session('access_token'))
		//$headers[] = 'Authorization: Bearer ' . $this->session('access_token');

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response);
	}
 	
 	/**
	* Get parameter via the URL
	*
	* @return bool
	*/
	public function get($key, $default=null) 
	{
		return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
	}
 	
 	/**
	* Get sesssion key
	*
	* @return bool
	*/
	public function session($key, $default=null) 
	{
		return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
	}

	/**
	* Generate Randomly for state
	*
	* @return string
	*/
	public function getState() 
	{ 
		return hash('sha256', microtime(true).rand());
	}

	/**
	* Create app source
	*
	* @return GramediaAccountClient
	*/
	public function setAppsource($appsourceWebsite, $appsourceSystem, $appsourceIpAddress) {
		$this->appsourceWebsite = $appsourceWebsite;
		$this->appsourceSystem = $appsourceSystem;
		$this->appsourceIpAddress = $appsourceIpAddress;
		return $this;
	}

	/**
	* Generate App Source Session
	*/
	public function getAppsource() {
		$appsource = array(
			'appsource_website' 	=> null,
			'appsource_system' 		=> null,
			'appsource_ip_address' 	=> null
		);
		return $_SESSION['appsource'] = $appsource;
	}

	/**
	* The host server oauth2
	*
	* @param string $appId The host server
	*
	* @return GramediaAccountClient
	*/
	public function setServer($server) {
		$this->server = $server;
		return $this;
	}

	/**
	* Get the Host Server OAuth2.
	*
	* @return string the Host Server
	*/
	public function getServer() {
		return $this->server;
	}

	/**
	* Set the Client ID.
	*
	* @param string $clientId The Client ID
	*
	* @return GramediaAccountClient
	*/
	public function setClientId($clientId) {
		$this->clientId = $clientId;
		return $this;
	}

	/**
	* Get the Client ID.
	*
	* @return string the Client ID
	*/
	public function getClientId() {
		return $this->clientId;
	}

	/**
	* Set the Client Secret.
	*
	* @param string $clientSecret The Client Secret
	*
	* @return GramediaAccountClient
	*/
	public function setClientSecret($clientSecret) {
		$this->clientSecret = $clientSecret;
		return $this;
	}

	/**
	* Get the Client Secret.
	*
	* @return string the Client Secret
	*/
	public function getClientSecret() {
		return $this->clientSecret;
	}

	/**
	* Set the Callback URL.
	*
	* @param string $callback The Callback URL
	*
	* @return GramediaAccountClient
	*/
	public function setCallback($callback) {
		$this->callback = $callback;
	}

	/**
	* Get the Callback URL.
	*
	* @return string the callback URL
	*/
	public function getCallback() {
		return $this->callback;
	}

	/**
	* Asset Fancybox
	*
	* @return string
	*/
	public static function asset($width = 400, $height = 400) 
	{
		$js = "
			<script type='text/javascript' src='".asset('assets/fancybox/jquery.fancybox.pack.js')."'></script>
			<link rel='stylesheet' type='text/css' href='".asset('assets/fancybox/jquery.fancybox.css')."' media='screen' />
			<script type='text/javascript'>
			    $(function() {
			        $('.gom_oauth_iframe').fancybox({
			            type: 'iframe',
			            autoDimensions: false,
			            width: ".$width.",
			            height: ".$height.",
			            overlayShow: true,
			            speedIn: 500, 
			            speedOut: 500,
			            transitionIn: 'elastic',
			            transitionOut: 'elastic',
			            afterClose: function () {
			                location.reload()
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
	* Configuration (Mandatory):
	* - required add class 'gom_oauth_iframe' in link
	* example <a href='#' class='gom_oauth_iframe'>Login</a>
	*
	* @param string callback URL
	* @return string
	*/
	public static function link($callback)
	{
		if (!empty($callback)) {
			return $callback;
		} 
		else {
			return false;
		}
	}
}
