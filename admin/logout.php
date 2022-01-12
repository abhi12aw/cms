<?php
include_once "../includes/functions.php";
_session_start();
unset($_SESSION['login']);
unset($_SESSION['user_id']);
$location = $site_url;
header( "Location: $location" );
exit;