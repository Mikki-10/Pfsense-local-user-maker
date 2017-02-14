<!DOCTYPE html>
<html> 
    <head>
        <title>Pfsense Grafer</title>
        <link rel="shortcut icon" href="favicon.ico" />
        
<?php

ini_set("display_errors", true);

include "funktioner.php";

if ($_POST != NULL) 
{
    var_dump($_POST);

    $pfsense = new pfsense;
    $pfsense->login($_POST["server"], $_POST["username"], $_POST["password"]);
    if (isset($_POST["delete_users"])) 
    {
        //Slet brugere
    }
    $pfsense->make_user($_POST["server"], $_POST["prefix"], isset($_POST["password_as_comment"]));
	//Vis resultat
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