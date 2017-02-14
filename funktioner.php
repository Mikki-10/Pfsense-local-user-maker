<?php

// --------------------------------------------------------- //
// Funktioner
// --------------------------------------------------------- //


/**
* 
*/
class pfsense
{
	function __construct()
	{
		$this->ch = curl_init();
	}

	function login($server, $username, $password)
	{
	    //login form action url
	    $url="https://". $server ."/index.php";

	    //set the directory for the webpage
	    $dir = dirname(__FILE__);

	    //set the directory for the cookie using defined document root var
	    //set in config.php
	    GLOBAL $cookie_file_path;

	    // Hvad vil du have retur?
	    //curl_setopt($ch, CURLOPT_HEADER, false);
	    //curl_setopt($ch, CURLOPT_URL, $url);

	    // Verificer SSL-certifikat?
	    curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

	    // Hvilken fil skal vi gemme cookies i?
	    curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	    curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie_file_path );

	    // Drop det her
	    //set the cookie the site has for certain features, this is optional
	    //curl_setopt($this->ch, CURLOPT_COOKIE, "cookiename=0");

	    // Set browser
	    curl_setopt($this->ch, CURLOPT_USERAGENT,
	        "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");

	    // Skal curl_exec() returnerer indholdet eller printe det?
	    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($this->ch,CURLOPT_HTTPHEADER,array("Expect:  "));
	    // Hvis vi modtager en header("Location: http://www.bla.dk")
	    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

	    curl_setopt($this->ch, CURLOPT_URL,$url);
	    $result=curl_exec($this->ch);
	    $ting = htmlentities($result);

	    //Tag kun csrf keyen
	    $getcsrf = scrape_between($result, "<input type='hidden' name='__csrf_magic' value=\"", '" />');
	    //var_dump($getcsrf);
	    //$getcsrf = urlencode($getcsrf);
	    if ($getcsrf == Null || $getcsrf == "") 
	    {
	        //Gør intet hvis den er logget ind.
	    }
	    else
	    {
	        //curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($this->ch, CURLOPT_POST, 1);

	        $postinfo = "usernamefld=".$username."&passwordfld=".$password."&__csrf_magic=".$getcsrf;
	        $postinfo = array(
	                'usernamefld'  =>  $username,
	                'passwordfld' => $password,
	                '__csrf_magic'  => $getcsrf,
	                'login' => "Login"
	            );

	        //curl_setopt($this->ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postinfo);
	        $html = curl_exec($this->ch);
	    }
	}
	   


	// --------------------------------------------------------- //
	// Funktion til at hente grafer
	// --------------------------------------------------------- //
	function make_user($server, $prefix, $password_as_comment = "false")
	{
	    //set the directory for the webpage
	    $dir = dirname(__FILE__);

	    //set the directory for the cookie using defined document root var
	    //set in config.php
	    GLOBAL $cookie_file_path;

	    //Find real url
	    $url = "https://". $server ."/user.php";

	    // Hvad vil du have retur?
	    //curl_setopt($this->ch, CURLOPT_HEADER, false);
	    //curl_setopt($this->ch, CURLOPT_URL, $url);

	    // Verificer SSL-certifikat?
	    curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

	    // Hvilken fil skal vi gemme cookies i?
	    curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	    curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie_file_path );


	    // Drop det her
	    //set the cookie the site has for certain features, this is optional
	    //curl_setopt($this->ch, CURLOPT_COOKIE, "cookiename=0");

	    // Set browser
	    GLOBAL $user_agent;
	    curl_setopt($this->ch, CURLOPT_USERAGENT,
	        "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");

	    // Skal curl_exec() returnerer indholdet eller printe det?
	    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($this->ch,CURLOPT_HTTPHEADER,array("Expect:  "));
	    // Hvis vi modtager en header("Location: http://www.bla.dk")
	    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

	    curl_setopt($this->ch, CURLOPT_POST, 0);
	    //page with the content I want to grab
	    curl_setopt($this->ch, CURLOPT_URL, $url);
	    //do stuff with the info with DomDocument() etc
	    $html = curl_exec($this->ch);
	    curl_close($this->ch);
	     
	    //var_dump($html);
	}
}

// --------------------------------------------------------- //
// Funktion til at logge ind
// --------------------------------------------------------- //

/**
* 
*/
class gui
{
	
	function __construct()
	{
		# code...
	}

	// --------------------------------------------------------- //
	// Vis GUI
	// --------------------------------------------------------- //
	function show_form()
	{
		?>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pure/0.6.2/pure-min.css">
		<link rel="stylesheet" type="text/css" href="/user-maker/css/simple.css">
		<div class="Aligner">
			<div class="Aligner-item">
				<form class="pure-form pure-form-aligned" method="post">
				    <fieldset>
				    	<div class="pure-control-group">
				            <label for="server">Server</label>
				            <input name="server" id="server" type="text" placeholder="Type server ip or domain" required>
				            <!--<span class="pure-form-message-inline">This is a required field.</span>-->
				        </div>

				        <div class="pure-control-group">
				            <label for="username">Username</label>
				            <input name="username" id="username" type="text" placeholder="Username" required>
				            <!--<span class="pure-form-message-inline">This is a required field.</span>-->
				        </div>

				        <div class="pure-control-group">
				            <label for="password">Password</label>
				            <input name="password" id="password" type="password" placeholder="Password" required>
				            <!--<span class="pure-form-message-inline">This is a required field.</span>-->
				        </div>

				        <div class="pure-control-group">
				            <label for="prefix">Prefix</label>
				            <input name="prefix" id="prefix" type="text" placeholder="username prefix e.g. tgvlan" required>
				            <!--<span class="pure-form-message-inline">This is a required field.</span>-->
				        </div>

				        <div class="pure-controls">
				            <label for="cb" class="pure-checkbox">
				                <input name="delete-users" id="cb" type="checkbox"> Delete ALL local users
				            </label>

				            <label for="cb" class="pure-checkbox">
				                <input name="password-as-comment" id="cb" type="checkbox" checked> Add user password as comment
				            </label>

				            <button type="submit" class="pure-button pure-button-primary">Submit</button>
				        </div>
				    </fieldset>
				</form>
			</div>
		</div>
		<?php
	}
}


// --------------------------------------------------------- //
// Funktion til at gemme kun den data man har behov for
// --------------------------------------------------------- //
// Defining the basic scraping function
function scrape_between($data, $start, $end)
{
    $data = stristr($data, $start); // Stripping all data from before $start
    $data = substr($data, strlen($start));  // Stripping $start
    $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;   // Returning the scraped data from the function
}

?>