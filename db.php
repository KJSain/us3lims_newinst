<?php

include 'config.php';

if ( preg_match( "/PASSW/", $dbpasswd ) )
{
  $mp_cmd   = exec( 'ls ~us3/scripts/map_password' );
  $dbpasswd = exec( "$mp_cmd $dbpasswd PW" );
}

$link = mysqli_connect( $dbhost, $dbusername, $dbpasswd, $dbname ) 
        or die("Could not connect to database server, db $dbhost, $dbname.");

?>
