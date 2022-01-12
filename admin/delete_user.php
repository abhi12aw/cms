<?php
///if page is directly access it redirect to the post source page
////This code should be in the file because this is a include file 

if (!defined('USER_PAGE_PART')) {
    if ($_SERVER['SCRIPT_NAME']) {
        $folder_name = explode('/', $_SERVER['SCRIPT_NAME']);
        $http_scheme = $_SERVER['REQUEST_SCHEME'] . '://';
        $http_host = $_SERVER['HTTP_HOST'];
        $location = "Location: " . $http_scheme . $http_host . '/' . $folder_name[1] . '/admin/user.php';
        header($location);
        exit;
    }
    exit;
}
if (_verify_nonces() !== true) {
    echo _verify_nonces();
} elseif (!isset($_GET['user_id']) || $_GET['user_id'] == '' ||  !_is_user($_GET['user_id'])) {
    echo "page not exist";
} elseif (isset($_GET['user_id']) && !($_GET['user_id'] == '') || _is_user($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $query = "DELETE FROM users WHERE user_id = $user_id";
    mysqli_query($db, $query);
    if( mysqli_affected_rows( $db ) > 0 )  {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['delete_success'] = true;
    }
    $location = $site_url . "admin/user.php?source=view_users";
    header("Location: $location");
    exit;
}
