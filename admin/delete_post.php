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
}
//if nonces not match
if (_verify_nonces() !== true) {
    echo _verify_nonces();
} elseif (!isset($_GET['post_id']) || !_is_post($_GET['post_id']) || $_GET['post_id'] == '') {
    echo "Post not exists";
} elseif( isset($_GET['post_id']) ) {
$post_id = $_GET['post_id'];

if( _is_post( $post_id ) )  {
    if( session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $delete_qurey =  "DELETE FROM posts WHERE post_id = ?";
    $stmt = $db->stmt_init();
    if( $stmt->prepare( $delete_qurey ) )  {
        $stmt->bind_param( 'i', $post_id );
        if( $stmt->execute() ) {
            $_SESSION['delete_success'] =  "Post deleted successfully";
        } else{
            $_SESSION['delete_error'] =  "Something went wrong";
        }
    } else {
       $_SESSION['delete_error'] =  "Something went wrong";
    }
    $location = $site_url . 'admin/posts.php?source=view_posts';
    header("Location: $location");
    exit;
}

}