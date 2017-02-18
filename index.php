<!DOCTYPE html>
<html> 
    <head>
        <title>Pfsense User Maker</title>
        <link rel="shortcut icon" href="favicon.ico" />
        
<?php

ini_set("display_errors", true);

//include "config.php";
include "functions.php";

if ($_POST != NULL) 
{
    //var_dump($_POST);

    $pfsense = new pfsense($_POST["server"], $_POST["http-https"]);
    
    $pfsense->login($_POST["username"], $_POST["password"]);

    if (isset($_POST["only-delete-users"])) 
    {
        $pfsense->delete_users($_POST["prefix"]);

        $gui = new gui;
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
    }
}
else
{
	$gui = new gui;
    $gui->show_form();
}



// --------------------------------------------------------- //
// Slut på kode
// --------------------------------------------------------- //



?>

 </body>
</html>