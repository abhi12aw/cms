<?php
///if page is directly access it redirect to the post source page
////This code should be in the file because this is a include file 

if (!defined('POST_PAGE_PART')) {
    if ($_SERVER['SCRIPT_NAME']) {
        $folder_name = explode('/', $_SERVER['SCRIPT_NAME']);
        $http_scheme = $_SERVER['REQUEST_SCHEME'] . '://';
        $http_host = $_SERVER['HTTP_HOST'];
        $location = "Location: " . $http_scheme . $http_host . '/' . $folder_name[1] . '/admin/posts.php?source=view_posts';
        header($location);
    }
    exit;
} ?>
Delete page