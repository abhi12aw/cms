<?php
ob_start();
include_once "../includes/db.php";
include_once "../includes/functions.php";
_session_start();
if( _is_logged_in() == false )  {
    $_SESSION['login_warning'] = true;
    $location = $site_url . "includes/login.php";
    header( "Location: $location" );
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $page_title ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdn.tiny.cloud/1/7engzu5milkl9xtdifm25rrns9omjpr16q1udcdz1ty9mvc5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
      tinymce.init({
        selector: '#post_content',
        plugins: 'advlist link image lists',
        toolbar: 'numlist bullist'
      });
    </script>
</head>

<body>