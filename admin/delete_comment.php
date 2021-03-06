<?php 
///if page is directly access it redirect to the post source page
////This code should be in the file because this is a include file 

if (!defined('COMMENT_PAGE_PART')) {
    if ($_SERVER['SCRIPT_NAME']) {
        $folder_name = explode('/', $_SERVER['SCRIPT_NAME']);
        $http_scheme = $_SERVER['REQUEST_SCHEME'] . '://';
        $http_host = $_SERVER['HTTP_HOST'];
        $location = "Location: " . $http_scheme . $http_host . '/' . $folder_name[1] . '/admin/comments.php';
        header($location);
        exit;
    }
    exit;
}

if (_verify_nonces() !== true) {
    echo _verify_nonces();
} elseif (!isset($_GET['comment_id']) || !_is_comment($_GET['comment_id'], false) || $_GET['comment_id'] == '') {
    echo "Post not exists";
} elseif( isset($_GET['comment_id']) ) {
 $comment_id = $_GET['comment_id'];
 $query = "DELETE FROM comments WHERE comment_id = ?"; 
 $stmt = $db->stmt_init();
 $stmt->prepare( $query );
 $stmt->bind_param( 'i', $comment_id );
 if($stmt->execute())  {
    $current_comment = _get_comments( ['comment_id' => $comment_id] );
    if( !empty( $current_comment ) )  {
        $current_comment_post_id = $current_comment[0]['comment_post_id'];
        comment_count_update( $current_comment_post_id );
    }
 }
 $location = $site_url . "admin/comments.php";
 header( "Location: $location" );
 exit;
}
