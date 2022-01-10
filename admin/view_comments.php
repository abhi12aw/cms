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
} ?>

<div class="">
    <table class="table table-hover">
        <thead>
            <tr>
                <td>ID</td>
                <td>Post</td>
                <td>Author</td>
                <td>Email</td>
                <td>Content</td>
                <td>Date</td>
                <td>Status</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $nonces = _create_nonces();
            $comments = _get_comments([], false);
            foreach( $comments as $comment )  {
             $comment_id = $comment['comment_id'];
             $comment_post_id = $comment['comment_post_id'];
             $comment_author = $comment['comment_author'];
             $comment_email = $comment['comment_email'];
             $comment_content = $comment['comment_content'];
             $comment_status = $comment['comment_status'];
             $comment_date = $comment['comment_date'];
             $comment_post = _get_post( $comment_post_id )[0];
             $comment_post_title = $comment_post['post_title'];
             $comment_post_permalink = $comment_post['post_permalink'];
            ?>
            <tr>
                <td><?= sanitize_op($comment_id) ?></td>
                <td><a href="<?= sanitize_op($comment_post_permalink) ?>" ><?= sanitize_op($comment_post_title) ?></a></td>
                <td><?= sanitize_op($comment_author) ?></td>
                <td><?= sanitize_op($comment_email) ?></td>
                <td><?= sanitize_op($comment_content) ?></td>
                <td><?= sanitize_op($comment_date) ?></td>
                <td><?php $is_approve = $comment_status == true ? 'unapprove' : 'approve'; 
                  echo "<a href='$site_url" . "admin/comments.php?source=$is_approve". "_comment&comment_id=$comment_id&nonces=$nonces'>"  .  sanitize_op( $is_approve )  . " it</a>";                      
                ?></td>
                <td><a href="<?= "$site_url" . "admin/comments.php?source=delete_comment" . "&comment_id=$comment_id&nonces=$nonces" ?>">Delete</a></td>
            </tr>
           <?php } ?> 
        </tbody>
    </table>
</div>