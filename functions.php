<?php

// --------------------------------------------------------- //
// Funktioner
// --------------------------------------------------------- //


/**
* 
*/
class pfsense
{
	function __construct($server, $http_https)
	{
		$this->server = $http_https . "://". $server;

		$this->ch = curl_init();

	    //set the directory for the webpage
	    //$dir = dirname(__FILE__);

	    //set the directory for the cookie using defined document root var
	    //set in config.php
	    //GLOBAL $cookie_file_path;

	    // Hvad vil du have retur?
	    //curl_setopt($ch, CURLOPT_HEADER, false);
	    //curl_setopt($ch, CURLOPT_URL, $url);

	    // Verificer SSL-certifikat?
	    curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);

	    // Hvilken fil/var skal vi load cookies fra?
	    $cookie = NULL;
	    curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie);
	    // Hvilken fil skal vi gemme cookies i?
	    //curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie_file_path);

	    // Drop det her
	    //set the cookie the site has for certain features, this is optional
	    //curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);

	    // Set browser
	    $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7";
	    curl_setopt($this->ch, CURLOPT_USERAGENT,
	        $user_agent);

	    // Skal curl_exec() returnerer indholdet eller printe det?
	    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($this->ch,CURLOPT_HTTPHEADER,array("Expect:  "));
	    // Hvis vi modtager en header("Location: http://www.bla.dk")
	    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
	}

	function login($username, $password)
	{
	    //login form action url
	    $url= $this->server ."/index.php";

	    curl_setopt($this->ch, CURLOPT_POST, 0);
	    curl_setopt($this->ch, CURLOPT_URL,$url);
	    $html=curl_exec($this->ch);
	    //$ting = htmlentities($html);

	    //Tag kun csrf keyen
	    $getcsrf = scrape_between($html, "<input type='hidden' name='__csrf_magic' value=\"", '" />');
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

	        //$postinfo = "usernamefld=".$username."&passwordfld=".$password."&__csrf_magic=".$getcsrf;
	        $postinfo = array(
	                'usernamefld'  	=> $username,
	                'passwordfld' 	=> $password,
	                '__csrf_magic'  => $getcsrf,
	                'login' => "Login"
	            );

	        //curl_setopt($this->ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postinfo);
	        curl_exec($this->ch);
	    }
	}
	  

	function delete_users($prefix)
	{
		$url = $this->server ."/system_usermanager.php";

		curl_setopt($this->ch, CURLOPT_URL, $url);
	    curl_setopt($this->ch, CURLOPT_POST, 0);
	    $html=curl_exec($this->ch);

		preg_match_all("/act=deluser.*\"/", $html, $output_array);

		$users = NULL;

		foreach ($output_array[0] as $key => $data) 
		{
			//echo "<pre>"; var_dump($data); echo "</pre>";

			$userid = scrape_between($data, "userid=", "&");
			$username_number = scrape_between($data, "username=" . $prefix, "\"");

			if ($username_number == "") 
			{
				//User do not match prefix
			}
			else
			{
				$users[$key] = array(
					'userid' => $userid,
					'username' => $prefix . $username_number,
					);
			}
		}

		if ($users != NULL) 
		{
			//echo "<pre>"; var_dump($users); echo "</pre>";

			foreach (array_reverse($users) as $key => $user) 
			{
				$url = $this->server . "/system_usermanager.php?act=deluser&userid=" . $user["userid"] . "&username=" . $user["username"];

				//echo $url . "<br>";

				curl_setopt($this->ch, CURLOPT_URL, $url);
				curl_setopt($this->ch, CURLOPT_POST, 0);
			    $html=curl_exec($this->ch);
			}
		}
		else
		{
			//No users match prefix
		}
	}   


	// --------------------------------------------------------- //
	// Funktion til at hente grafer
	// --------------------------------------------------------- //
	function make_user($prefix, $user_amount, $password_as_comment = "false")
	{
	    //Find real url
	    $url = $this->server ."/system_usermanager.php?act=new";

	    curl_setopt($this->ch, CURLOPT_POST, 0);
	    curl_setopt($this->ch, CURLOPT_URL,$url);
	    $result=curl_exec($this->ch);
	    $ting = htmlentities($result);

	    //Tag kun csrf keyen
	    $getcsrf = scrape_between($result, "<input type='hidden' name='__csrf_magic' value=\"", '" />');

	    curl_setopt($this->ch, CURLOPT_URL, $url);
	    //do stuff with the info with DomDocument() etc
	    //curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->ch, CURLOPT_POST, 1);

        $users = NULL;

        for ($i=1; $i < $user_amount+1; $i++) 
        { 
        	$username = $prefix . $i;
        	$password = mt_rand(100000,999999);

	        //$postinfo = "usernamefld=".$username."&passwordfld=".$password."&__csrf_magic=".$getcsrf;
	        if ($password_as_comment == "true") 
	        {
	        	$postinfo = array(
	                '__csrf_magic'  => $getcsrf,
	                'usernamefld'  	=> $username,
	                'passwordfld1' 	=> $password,
	                'passwordfld2' 	=> $password,
	                'descr' 		=> "Password: " . $password,
	                'utype' 		=> "user",
	                'save' 			=> "Save",
	            );
	        }
	        else
	        {
	       		$postinfo = array(
	                '__csrf_magic'  => $getcsrf,
	                'usernamefld'  	=> $username,
	                'passwordfld1' 	=> $password,
	                'passwordfld2' 	=> $password,
	                'utype' 		=> "user",
	                'save' 			=> "Save",
	            );
	        }

	        //curl_setopt($this->ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postinfo);
	        curl_exec($this->ch);

	        $curl_info = curl_getinfo($this->ch);

	        //$users[$i] = array($username, $password);
	        $users[$i-1] = array(
	        					'username' 	=> $username,
	        					'password' 	=> $password,
	        					'http_code'	=> $curl_info["http_code"]
	        					);
        }

        return $users;

	    //var_dump($html);
	}

	function __destruct() 
	{
       curl_close($this->ch);
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
	function show_form($only_delete_users = false)
	{
		?>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pure/0.6.2/pure-min.css">
		<link rel="stylesheet" type="text/css" href="/user-maker/css/simple.css">
		<div class="Aligner">
			<div class="Aligner-item">
				<form class="pure-form pure-form-aligned" method="post">
				    <fieldset>
				        <?php

				        if ($only_delete_users === true) 
				        {
				        	?>
				        	<div class="pure-controls" style="margin-top: 0px;">
				            	<b style="color:#FF0000">&nbspUsers deleted (prefix<?php if(isset($_POST["prefix"]) == true && $_POST["prefix"] != "") { echo " " . $_POST["prefix"]; } ?>)</b>
				        	</div>
				        	<?php
				        }

				        ?>

				    	<div class="pure-control-group">
				            <label for="server">Server</label>
				            <input name="server" id="server" type="text" placeholder="Type server ip or domain" required>
				            <!--<span class="pure-form-message-inline">This is a required field.</span>-->
				        </div>

				        <div class="pure-controls" style="margin-top: 0px;">
				        	<label for="rd" class="pure-radio">
					        	<input type="radio" id="rd" name="http-https" value="https" checked> https
					        	<input type="radio" id="rd" name="http-https" value="http"> http
	  						</label>
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

				        <div class="pure-control-group">
				            <label for="user-amount">User amount</label>
				            <input type="number" id="user-amount" name="user-amount" value="340" min="1" max="500" required>
				            <!--<span class="pure-form-message-inline">This is a required field.</span>-->
				        </div>

				        <div class="pure-controls" style="margin-top: 0px;">
				            <label for="cb" class="pure-checkbox">
				                <input name="delete-users" id="cb" type="checkbox" checked> Delete all local users whit prefix
				            </label>

				            <label for="cb" class="pure-checkbox">
				                <input name="password-as-comment" id="cb" type="checkbox" checked> Add user password as comment
				            </label>

				            <button type="submit" name="submit" class="pure-button pure-button-primary">Submit</button>
				            <button type="submit" name="only-delete-users" class="pure-button pure-button-primary">Only delete users</button>
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
    return $data;  // Returning the scraped data from the function
}

// --------------------------------------------------------- //
// Funktion til at gemme kun den data man har behov for
// --------------------------------------------------------- //
// Defining the basic scraping function
function scrape_to($data, $end)
{
    //$data = stristr($data, $start); // Stripping all data from before $start
    //$data = substr($data, strlen($start));  // Stripping $start
    $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;  // Returning the scraped data from the function
}

// --------------------------------------------------------- //
// Funktion til at gemme kun den data man har behov for
// --------------------------------------------------------- //
// Defining the basic scraping function
function scrape_from($data, $start)
{
    $data = stristr($data, $start); // Stripping all data from before $start
    $data = substr($data, strlen($start));  // Stripping $start
    //$stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    //$data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;  // Returning the scraped data from the function
}

?>