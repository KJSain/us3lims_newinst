<?php
/*
 * makeconfig.php
 *
 * Creates a config.php file
 *
 */
session_start();

/*
// Are we authorized to view this page?
if ( ! isset($_SESSION['id']) )
{
  header('Location: index.php');
  exit();
} 

if ( ($_SESSION['userlevel'] != 4) &&
     ($_SESSION['userlevel'] != 5) )    // admin and super admin only
{
  header('Location: index.php');
  exit();
} 
*/

include 'config.php';
include 'db.php';

// Make sure there is a parameter
if ( $_SERVER['argc'] != 4 )
{
  echo "Usage: php makeconfig.php <db_name>";
  exit();
}

$new_dbname     = $_SERVER['argv'][1];
$new_orgsite    = $_SERVER['argv'][2];
$new_ipaddress  = $_SERVER['argv'][3];


$query  = "SELECT institution, dbuser, dbpasswd, dbhost, " .
          "secure_user, secure_pw, " .
          "admin_fname, admin_lname, admin_email, admin_pw, lab_contact " .
          "FROM metadata " .
          "WHERE dbname = '$new_dbname' ";

$result = mysqli_query($link, $query) 
          or die("Query failed : $query<br />\n" . mysqli_error($link));

if ( mysqli_num_rows( $result ) != 1 )
{
  echo "$new_dbname not found\n";
  exit();
}

list( $institution,
      $new_dbuser,
      $new_dbpasswd,
      $new_dbhost,
      $secure_user,
      $secure_pw,
      $admin_fname,
      $admin_lname,
      $admin_email,
      $admin_pw,
      $lab_contact )   = mysqli_fetch_array( $result );

$today  = date("Y\/m\/d");
$year   = date( "Y" );

#$lab_contact = preg_replace( "/\r|\n/", "<br />", $lab_contact );
$lab_contact = preg_replace( "/\r/", "<br />", $lab_contact );

$text = <<<TEXT
<?php
/*  Database and other configuration information - Required!!  
 -- Configure the Variables Below --

*/

\$org_name           = '$org_name';
\$org_site           = '$new_orgsite/$new_dbname';
\$site_author        = '$site_author';
\$site_keywords      = '$site_keywords';
                      # The website keywords (meta tag)
\$site_desc          = 'Website for the UltraScan3 LIMS portal'; # Site description

\$admin              = '$admin_fname $admin_lname';
\$admin_phone        = '$lab_contact'; #'Office: <br />Fax: ';
\$admin_email        = '$admin_email';

\$dbusername         = '$new_dbuser';  # the name of the MySQL user
\$dbpasswd           = '$new_dbpasswd';  # the password for the MySQL user
\$dbname             = '$new_dbname';  # the name of the database
\$dbhost             = 'localhost'; # the host on which MySQL runs, generally localhost

// Secure user credentials
\$secure_user        = '$secure_user'; # the secure username that UltraScan3 uses
\$secure_pw          = '$secure_pw';   # the secure password that UltraScan3 uses

// Global DB
\$globaldbuser       = '$globaldbuser';  # the name of the MySQL user
\$globaldbpasswd     = '$globaldbpasswd';  # the password for the MySQL user
\$globaldbname       = '$globaldbname';  # the name of the database
\$globaldbhost       = 'localhost'; # the host on which MySQL runs, generally localhost

// Admin function
\$v1_host            = "localhost";
\$v1_user            = "root";
\$v1_pass            = "";

\$v2_host            = "localhost";
\$v2_user            = "lims3_admin";
\$v2_pass            = "";

\$ipaddr             = '$new_ipaddress'; # the primary IP address of the host machine
\$ipa_ext            = '$ipaddr'; # the external IP address of the host machine
\$ipad_a             = '10.54.0.1';       # the primary IP address of the host (alamo)
\$ipae_a             = '10.115.127.212';  # the external IP address of the host (alamo)
\$udpport            = $udpport; # the port to send udp messages to
\$svcport            = $svcport;  # the port for GFAC/Airavata services
\$uses_thrift        = $uses_thrift;  # flags use of Thrift rather than Gfac
\$thr_clust_excls    = array( 'bcf' ); # Never uses Thrift
\$thr_clust_incls    = array( 'alamo' ); # Always uses Thrift

\$top_image          = '#';  # name of the logo to use
\$top_banner         = 'images/#';  # name of the banner at the top

\$full_path          = '$dest_path$new_dbname/';  # Location of the system code
\$data_dir           = '$dest_path$new_dbname/data/'; # Full path
\$submit_dir         = '${dest_path}uslims3/uslims3_data/'; # Full path
\$class_dir          = '${dest_path}/common/class/';       # Production class path
//\$class_dir          = '/srv/www/htdocs/common/class_devel/'; # Development class path
\$disclaimer_file    = ''; # the name of a text file with disclaimer info

// Dates
date_default_timezone_set( '${timezone}' );
\$last_update        = '$today'; # the date the website was last updated
\$copyright_date     = '$year'; # copyright date
\$current_year       = date( 'Y' );

//////////// End of user specific configuration

// ensure a trailing slash
if ( \$data_dir[strlen(\$data_dir) - 1] != '/' )
  \$data_dir .= '/';

if ( \$submit_dir[strlen(\$submit_dir) - 1] != '/' )
  \$submit_dir .= '/';

if ( \$class_dir[strlen(\$class_dir) - 1] != '/' )
  \$class_dir .= '/';

/* Define our file paths */
if ( ! defined('HOME_DIR') ) 
{
  define('HOME_DIR', \$full_path );
}

if ( ! defined('DEBUG') ) 
{
  define('DEBUG', false );
}

?>
TEXT;

if ( file_exists( $dest_path . $new_dbname ) )
  file_put_contents( $dest_path . "$new_dbname/config.php", $text );

else
{
  global $data_dir;

  file_put_contents( $data_dir . 'config.php', $text );
}

?>
