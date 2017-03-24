<!DOCTYPE html>
<html> 
    <head>
        <title>Pfsense User Maker</title>
        <link rel="shortcut icon" href="favicon.ico" />
        
<?php

//ini_set("display_errors", true);

include "functions.php";

$gui = new gui;

if ($_POST != NULL) 
{
    //var_dump($_POST);

    $pfsense = new pfsense($_POST["server"], $_POST["http-https"]);
    
    $pfsense->login($_POST["username"], $_POST["password"]);

    if (isset($_POST["only-delete-users"])) 
    {
        $pfsense->delete_users($_POST["prefix"]);

        $gui->show_form(TRUE);
    }
    elseif (isset($_POST["submit"]))
    {
        if (isset($_POST["delete-users"])) 
        {
            $pfsense->delete_users($_POST["prefix"]);
        }

        $users = $pfsense->make_user($_POST["prefix"], $_POST["user-amount"], isset($_POST["password-as-comment"]));
        //Vis resultat
        //echo "<pre>"; var_dump($users); echo "</pre>";
        
        /*
        foreach ($users as $key => $user) 
        {
            echo "Username: ";
            echo $user["username"];
            echo "<br>";
            echo "Password: ";
            echo $user["password"];
            echo "<br>";
            echo "<br>";
        }
        */

        $gui->page_print($_POST["output-format"], $users);
    }
}
else
{
    $gui->show_form();
    //$gui->print("print", array(array("username" => "tgvlan1", "password" => "1234"), array("username" => "tgvlan2", "password" => "2345"), array("username" => "tgvlan3", "password" => "3456")) );
}

// Clean up old files uploaded
/** define the directory **/
$dir = __DIR__ . "/uploads/";

/*** cycle through all files in the directory ***/
foreach (glob($dir."*") as $file) {

/*** if file is 24 hours (86400 seconds) old then delete it ***/
if (filemtime($file) < time() - 86400) {
    unlink($file);
    }
}



// --------------------------------------------------------- //
// Slut på kode
// --------------------------------------------------------- //



?>

 </body>
</html>