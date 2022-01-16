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

if (isset($_POST['bulk_select'])) {
    ///Allowed action
    $action = ['published' => 'Published', 'draft' => 'Draft', 'delete' => 'Delete', 'clone' => 'Clone'];
    // _print_r($_POST);
    if ((isset($_POST['bulk_select_action']) && in_array($_POST['bulk_select_action'], $action)) && (isset($_POST['checkItem']) && $_POST['checkItem'] != '')) {
        $bulk_action = strtolower($_POST['bulk_select_action']);
        $bulk_query = '';
        $opration_list = [];
        $stmt = $db->stmt_init();
        switch ($bulk_action) {
            case 'published':
                $bulk_query = "UPDATE posts SET post_status = 'published' WHERE post_id = ?";
                break;
            case 'draft':
                $bulk_query = "UPDATE posts SET post_status = 'draft' WHERE post_id = ?";
                break;
            case 'delete':
                $bulk_query = "DELETE FROM posts WHERE post_id = ?";
                break;
            case 'clone':
                $bulk_query = "INSERT INTO posts (post_category_id, post_title, post_date, post_image, post_content, post_tag ) VALUES (?, ?, ?, ?, ?, ?)";
                // echo "clone";
                break;
        }
        $stmt->prepare($bulk_query);
        foreach ($_POST['checkItem'] as $item) {
            if ($bulk_action == 'clone') {
                $clone_post = _get_post($item)[0];
                $clone_post_category_id = $clone_post['post_category_id'];
                $clone_post_title = $clone_post['post_title'];
                $clone_post_date = date('d-m-y');
                $clone_post_image = $clone_post['post_image'];
                $clone_post_content = $clone_post['post_content'];
                $clone_post_tag = $clone_post['post_tag'];
                if (!empty($clone_post_tag)) {
                    if (!preg_match('/,/', $clone_post_tag)) {
                        $clone_post_tag = explode(' ', $clone_post_tag);
                        $clone_post_tag = serialize($clone_post_tag);
                    }
                }
                $stmt->bind_param('isssss', $clone_post_category_id, $clone_post_title, $clone_post_date, $clone_post_image, $clone_post_content, $clone_post_tag);
            } elseif (in_array($_POST['bulk_select_action'], $action)) {
                $stmt->bind_param('i', $item);
            }
            $stmt->execute();
            if (mysqli_affected_rows($db) > 0) {
                $opration_list[$item] = 'true';
            } else {
                $opration_list[$item] = 'false';
            }
        }
        //    _print_r( $opration_list );
    }
}

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
<form action="" method="post">
    <div style="margin-bottom: 5px" class="row">
        <div class="col-lg-6">
            <div class="input-group">
                <select class="form-control" name="bulk_select_action" id="">
                    <option value="Published">Published</option>
                    <option value="Draft">Draft</option>
                    <option value="Delete">Delete</option>
                    <option value="Clone">Clone</option>
                </select>
                <span class="input-group-btn">
                    <input style="margin-left: -4px; z-index: 0;" class="btn btn-info" type="submit" value="Bulk Action" name="bulk_select">
                </span>
                <span class="input-group-btn">
                    <a style="margin-left: 10px; border-radius: 5px; outline: 0px" class="btn btn-primary" href="<?= $site_url ?>admin/posts.php?source=add_post">Add New Post</a>
                </span>
            </div>
        </div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <td><input type="checkbox" name="checkAllBox" id="checkAllBox" value=""></td>
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
                    $post_status = ucfirst($post['post_status']);
            ?>
                    <tr>
                        <td><input type="checkbox" id="checkItem" name="checkItem[]" value="<?= sanitize_op($post_id) ?>"></td>
                        <td><?= sanitize_op($post_id) ?></td>
                        <td><?= sanitize_op($post_author) ?></td>
                        <td><?= sanitize_op($post_title) ?></td>
                        <td><?= sanitize_op($post_category) ?></td>
                        <td><?= sanitize_op($post_status) ?></td>
                        <?php if ($post_image != '') { ?>
                            <td><img style="height: 100px; object-fit: cover;" class="img-responsive" src="<?php echo $upload_image_url . sanitize_op($post_image) ?>"></td>
                        <?php } else {
                            echo "<td></td>";
                        } ?>
                        <td><?php if (is_array($post_tag)) {
                                $tags = '';
                                foreach ($post_tag as $tag) {
                                    $tags .= $tag . ' ';
                                }
                                echo sanitize_op($tags);
                            } else {
                                echo sanitize_op($post_tag);
                            } ?></td>
                        <td><?= sanitize_op($post_comment) ?></td>
                        <td><?= sanitize_op($post_date) ?></td>
                        <td><a href="<?= $site_url ?>post.php?post_id=<?= sanitize_op($post_id) ?>">View</a> &nbsp;
                            <a href="<?= $current_page ?>?source=edit_post&post_id=<?= sanitize_op($post_id) ?>&nonces=<?= $nonces ?>">Edit</a> &nbsp;
                            <a href="<?= $current_page ?>?source=delete_post&post_id=<?= sanitize_op($post_id) ?>&nonces=<?= $nonces ?>">Delete</a>
                        </td>
                    </tr>
            <?php     }
            }
            ?>
        </tbody>
    </table>
</form>