<!DOCTYPE html>
<html> 
    <head>
        <title>Pfsense Grafer</title>
        <link rel="shortcut icon" href="favicon.ico" />
        
<?php

ini_set("display_errors", true);

if (isset($_POST)) 
{
	//Modtag indput
	//Behandel
		//Mangler nogle input
			//Vis tart GUI
	//Vis resultat
}
else
{
	//Vis start GUI
}


// --------------------------------------------------------- //
// Funktioner
// --------------------------------------------------------- //




// --------------------------------------------------------- //
// Funktion til at logge ind
// --------------------------------------------------------- //
function login($ip, $username, $password)
{
    //login form action url
    $url="https://". $ip ."/index.php";

    //set the directory for the webpage
    $dir = dirname(__FILE__);

    //set the directory for the cookie using defined document root var
    $cookie_file_path = "C://temp/cookie-".$ip.".txt";


    $ch = curl_init();

    // Hvad vil du have retur?
    //curl_setopt($ch, CURLOPT_HEADER, false);
    //curl_setopt($ch, CURLOPT_URL, $url);

    // Verificer SSL-certifikat?
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    // Hvilken fil skal vi gemme cookies i?
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path );

    // Drop det her
    //set the cookie the site has for certain features, this is optional
    //curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");

    // Set browser
    curl_setopt($ch, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");

    // Skal curl_exec() returnerer indholdet eller printe det?
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,array("Expect:  "));
    // Hvis vi modtager en header("Location: http://www.bla.dk")
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    curl_setopt($ch, CURLOPT_URL,$url);
    $result=curl_exec($ch);
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
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);

        $postinfo = "usernamefld=".$username."&passwordfld=".$password."&__csrf_magic=".$getcsrf;
        $postinfo = array(
                'usernamefld'  =>  $username,
                'passwordfld' => $password,
                '__csrf_magic'  => $getcsrf,
                'login' => "Login"
            );

        //curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $html = curl_exec($ch);
    }
}
   


// --------------------------------------------------------- //
// Funktion til at hente grafer
// --------------------------------------------------------- //
function post_user($ip)
{
    //set the directory for the webpage
    $dir = dirname(__FILE__);

    //set the directory for the cookie using defined document root var
    $cookie_file_path = "C://temp/cookie-".$ip.".txt";


    $ch = curl_init();

    // Hvad vil du have retur?
    //curl_setopt($ch, CURLOPT_HEADER, false);
    //curl_setopt($ch, CURLOPT_URL, $url);

    // Verificer SSL-certifikat?
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    // Hvilken fil skal vi gemme cookies i?
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path );


    // Drop det her
    //set the cookie the site has for certain features, this is optional
    //curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");

    // Set browser
    curl_setopt($ch, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");

    // Skal curl_exec() returnerer indholdet eller printe det?
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,array("Expect:  "));
    // Hvis vi modtager en header("Location: http://www.bla.dk")
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    curl_setopt($ch, CURLOPT_POST, 0);
    //page with the content I want to grab
    curl_setopt($ch, CURLOPT_URL, $graf_to_get);
    //do stuff with the info with DomDocument() etc
    $html = curl_exec($ch);
    curl_close($ch);
     
    var_dump($html);
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


// --------------------------------------------------------- //
// Slut på kode
// --------------------------------------------------------- //



?>

 </body>
</html>