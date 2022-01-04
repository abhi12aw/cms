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

<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (isset($_SESSION['delete_success'])) { ?>
    <div class="alert alert-success" role="alert">
        <?= $_SESSION['delete_success'] ?>
    </div>
<?php
    unset($_SESSION['delete_success']);
}
if (isset($_SESSION['delete_error'])) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $_SESSION['delete_error'] ?>
    </div>
<?php
    unset($_SESSION['delete_error']);
} ?>
<div class="">
    <table class="table table-hover">
        <thead>
            <tr>
                <td>ID</td>
                <td>Author</td>
                <td>Title</td>
                <td>Category</td>
                <td>Status</td>
                <td>Image</td>
                <td>Tags</td>
                <td>Comments</td>
                <td>Date</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $posts = _get_all_posts();
            if ($posts == true) {
                $nonces = _create_nonces();
                foreach ($posts as $post) {
                    $post_id = $post['post_id'];
                    if ($post['post_category_id'] != '') {
                        $post_category = _get_category($post['post_category_id']);
                        if (!$post_category) $post_category = "";
                    } else {
                        $post_category = '';
                    }
                    $post_title = $post['post_title'];
                    $post_author = $post['post_author'];
                    $post_date = $post['post_date'];
                    $post_image = $post['post_image'];
                    $post_content = $post['post_content'];
                    $post_tag = unserialize($post['post_tag']);
                    $post_comment = $post['post_comment_count'];
                    $post_status = $post['post_status'];
            ?>
                    <tr>
                        <td><?= trim(htmlentities($post_id, ENT_QUOTES)) ?></td>
                        <td><?= trim(htmlentities($post_author, ENT_QUOTES)) ?></td>
                        <td><?= trim(htmlentities($post_title, ENT_QUOTES)) ?></td>
                        <td><?= trim(htmlentities($post_category, ENT_QUOTES)) ?></td>
                        <td><?= trim(htmlentities($post_status, ENT_QUOTES)) ?></td>
                        <td><img style="height: 100px; object-fit: cover;" class="img-responsive" src="<?php echo $upload_image_url . trim(htmlentities($post_image, ENT_QUOTES)) ?>"></td>
                        <td><?php if (is_array($post_tag)) {
                                $tags = '';
                                foreach ($post_tag as $tag) {
                                    $tags .= $tag . ' ';
                                }
                                echo trim(htmlentities($tags, ENT_QUOTES));
                            } else {
                                echo trim(htmlentities($post_tag, ENT_QUOTES));
                            } ?></td>
                        <td><?= trim(htmlentities($post_comment, ENT_QUOTES)) ?></td>
                        <td><?= trim(htmlentities($post_date, ENT_QUOTES)) ?></td>
                        <td><a href="<?= $current_page ?>?source=edit_post&post_id=<?= trim(htmlentities($post_id, ENT_QUOTES)) ?>&nonces=<?= $nonces ?>">Edit</a> &nbsp;
                            <a href="<?= $current_page ?>?source=delete_post&post_id=<?= trim(htmlentities($post_id, ENT_QUOTES)) ?>&nonces=<?= $nonces ?>">Delete</a>
                        </td>
                    </tr>
            <?php     }
            }
            ?>
        </tbody>
    </table>
</div>