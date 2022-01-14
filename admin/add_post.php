<?php
////If page is directly access it redirect to the post source page( ie post.php?source=add_post )
////This code should be in the file because this is a include file 
if (!defined('POST_PAGE_PART')) {
    if ($_SERVER['SCRIPT_NAME']) {
        $folder_name = explode('/', $_SERVER['SCRIPT_NAME']);
        $http_scheme = $_SERVER['REQUEST_SCHEME'] . '://';
        $http_host = $_SERVER['HTTP_HOST'];
        $location = "Location: " . $http_scheme . $http_host . '/' . $folder_name[1] . '/admin/posts.php?source=add_post';
        header($location);
    }
    exit;
}

$post_publish_option = _get_post_publish_options();
if (isset($_POST['add_post'])) {
    $post_title = $_POST['post_title'];
    $post_category = $_POST['post_category'];
    $post_author = _get_username(_get_current_user_id());
    $post_image = $_FILES['post_image'];
    $post_image_allowed_type = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];
    $post_tmp_folder = $_FILES['post_image']['tmp_name'];
    $post_content = sanitize_op($_POST['post_content']);
    $post_tag = $_POST['post_tag'];
    $post_status = $_POST['post_status'];
    $post_date = date('d-m-y');
    $post_data_required = ['post_title' => $post_title, 'post_author' => $post_author];
    $post_error = [];
    $post_error_massage = "This field is empty or something went wrong please try again";
    // print_r($post_data_required);
    foreach ($post_data_required  as $key => $required) {
        if (empty($required)) {
            $post_error[] = $key;
        }
    }
    //check the value of category coming from the post if it's there in database
    if (!_in_category($post_category)) $post_error[] = 'post_category';
    // _print_r($post_error);

    ///post tags check
    if (!empty($post_tag)) {
        //if comma is in the string
        if (preg_match('/,/', $post_tag)) {
            $post_error['post_tag'] = "Post tag cannot contain commans";
        } elseif (!preg_match('/,/', $post_tag)) {
            $separate_post_tag = explode(' ', $post_tag);
            $separate_post_tag = serialize($separate_post_tag);
        }
    }

    // _print_r($post_image);
    ///post image check 
    if ($_FILES['post_image']['size'] !== 0 && empty($post_error)) {
        $_is_error_found_image = true;
        ///default is true
        switch ($_FILES['post_image']['error']) {
            case 0:
                $_is_error_found_image = false;
                break;
            case 1:
            case 2:
                $post_error['post_image'] = "File size exceeds";
                break;
            case 4:
                $post_error['post_image'] = "Image not uploaded fully please try again";
                break;
            case 6:
            case 8:
                $post_error['post_image'] = "Something went wrong";
                break;
            case 7:
                $post_error['post_image'] = "Failed to write file to disk";
                break;
            default:
                $post_error['post_image'] = "Something went wrong";
        }
        if ($_is_error_found_image == false) {
            $image_type = $post_image['type'];
            if (in_array($image_type, $post_image_allowed_type)) {
                $post_image_name = trim($post_image['name']);
                $post_image_name = explode(' ', $post_image_name);
                $post_image_name = implode('-', $post_image_name);
                //if the file name already exist in the folder change the file name
                if (file_exists($upload_image_dir . $post_image_name)) {
                    if (change_file_name_if_exist($upload_image_dir, $post_image_name) != false) {
                        $post_image_name = change_file_name_if_exist($upload_image_dir, $post_image_name);
                    } elseif (change_file_name_if_exist($upload_image_dir, $post_image_name) == false) {
                        $post_error['post_image']  = "The uploaded file is not valid or Something went wrong please try again";
                    }
                }
                if (!array_key_exists('post_image', $post_error)) {
                    if (move_uploaded_file($post_tmp_folder, $upload_image_dir . $post_image_name)) {
                    } else {
                        $post_error['post_image']  = "Image not uploaded properly";
                    }
                }
            } elseif (!in_array($image_type, $post_image_allowed_type)) {
                $post_error['post_image']  = "File type not allowed";
            }
        }
    }
    if (empty($post_error)) {
        if( $_FILES['post_image']['size'] == 0 )  {
            $post_image_name = '';
        }
        $post_status = in_array($post_status, $post_publish_option) ? $post_status : $post_publish_option[0];
        $post_db_query = "INSERT INTO posts ( post_category_id, post_title, post_author, post_date, post_image, post_content, post_tag, post_status)
        VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )";
        $stmt = $db->stmt_init();
        if ($stmt->prepare($post_db_query)) {
            $stmt->bind_param('isssssss', $post_category, $post_title, $post_author, $post_date, $post_image_name, $post_content, $separate_post_tag, $post_status);
            if ($stmt->execute() && mysqli_affected_rows( $db ) > 0) {
                $post_id = mysqli_insert_id( $db );
                $nonces = _create_nonces();
                $location = $site_url . "admin/posts.php?source=edit_post&post_id=$post_id&nonces=$nonces";
                $_SESSION['page_added'] = true;
                $_SESSION['page_added_id'] = $post_id;
                header( "Location: $location" );
                exit;
            } else {
                echo $stmt->error;
            }
        } else {
            echo $stmt->error;
        }
    }
}
?>
<?php if(isset($post_message)) {?>
<div class="alert alert-success" role="alert">
     <?= $post_message ?>
</div>
<?php } ?>
<form action="<?= $site_url . 'admin/posts.php?source=add_post' ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="post_title">Post Title</label>
        <?php
        if (isset($post_error) && in_array('post_title', $post_error)) { ?>
            <div class="alert alert-danger" role="alert"><?= $post_error_massage ?></div>
        <?php } ?>
        <input class="form-control" type="text" name="post_title" id="post_title" <?php if (isset($post_title)) echo 'value="' . trim(htmlentities($post_title, ENT_QUOTES)) . '"' ?>>
    </div>
    <div class="form-group">
        <label for="post_category">Post Category</label>
        <?php
        if (isset($post_error) && in_array('post_category', $post_error)) { ?>
            <div class="alert alert-danger" role="alert"><?= $post_error_massage ?></div>
        <?php } ?>
        <select class="form-control" name="post_category" id="post_category">
            <?php
            $categories = _get_all_category();
            foreach ($categories as $category) {
                if (isset($post_category) && $category['cat_id'] == $post_category) {
                    $if_category_selected = 'selected';
                } else $if_category_selected = '';
            ?>
                <option <?= $if_category_selected ?> value="<?= $category['cat_id'] ?>"><?= $category['cat_title'] ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <label for="post_image">Image</label>
        <?php
        if (isset($post_error) && array_key_exists('post_image', $post_error)) { ?>
            <div class="alert alert-danger" role="alert"><?= $post_error['post_image'] ?></div>
        <?php } ?>
        <input class="form-control" type="file" name="post_image" id="post_image">
    </div>
    <div class="form-group">
        <?php
        if (isset($post_error) && in_array('post_content', $post_error)) { ?>
            <div class="alert alert-danger" role="alert"><?= $post_error_massage ?></div>
        <?php } ?>
        <label for="post_content">Content</label>
        <textarea class="form-control" name="post_content" id="post_content"><?php if (isset($post_content)) echo html_entity_decode($post_content, ENT_QUOTES) ?></textarea>
    </div>
    <div class="form-group">
        <label for="post_tag">Post Tag (seprate tags with whitespace)</label>
        <?php
        if (isset($post_error) && array_key_exists('post_tag', $post_error)) { ?>
            <div class="alert alert-danger" role="alert"><?= $post_error['post_tag'] ?></div>
        <?php } ?>
        <input class="form-control" type="text" name="post_tag" id="post_tag" <?php if (isset($post_tag) && is_string($post_tag)) echo 'value="' . trim(htmlentities($post_tag, ENT_QUOTES)) . '"' ?>>
    </div>
    <div class="form-group">
        <label for="post_status">Post Status</label>
        <select class="form-control" name="post_status" id="post_status" type="text">
            <?php
            foreach ($post_publish_option as $option) { ?>
                <option value="<?= $option ?>" <?php if( isset( $post_status ) && $post_status == $option ) echo "selected" ?>><?= ucfirst($option) ?></option>
            <?php } ?>
        </select>
    </div>
    <input style="margin-top: 20px" class="btn btn-primary" name="add_post" type="submit" value="Add Post">
</form>