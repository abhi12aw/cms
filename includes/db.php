<?php
/** 
 * This is the Database file
*/

define( "DB_HOSTNAME", 'localhost' );
define( "DB_USERNAME", 'root' );
define( "DB_PASSWORD", '' );
define( "DB_NAME", 'cms' );

/* You should enable error reporting for mysqli before attempting to make a connection */
mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
$db =  new mysqli( DB_HOSTNAME, DB_USERNAME, DB_PASSWORD,  DB_NAME );

if( !$db )  {
    error_log( 'Connection error:' . mysqli_connect_errno() );
}