<?php

include 'config.php';

$link = mysqli_connect( $dbhost, $dbusername, $dbpasswd, $dbname ) 
        or die( mysqli_query($link));

/*mysqli_select_db($link, $dbname) 
        or die("Could not select database. " .
	"Please ask your Database Administrator for help."); */
?>
