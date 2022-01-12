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
} ?>

<div class="">
    <?php
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (isset($_SESSION['delete_success'])) { ?>
        <div class="alert alert-success"><?= "User deleted successfully" ?></div>
    <?php unset($_SESSION['delete_success']);
    } ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <td>ID</td>
                <td>User image</td>
                <td>Username</td>
                <td>First Name</td>
                <td>Last Name</td>
                <td>Email</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $all_users = _get_users();
            $nonces = _create_nonces();
            //   _print_r( $all_users );
            foreach ($all_users as $user) {
                $user_id =  $user['user_id'];
                $user_name = $user['user_name'];
                $user_firstname = $user['user_firstname'];
                $user_image = $user['user_image'];
                if ($user_image == '') {
                    $user_image = $cms_image_folder_url . 'placeholder.jpg';
                }
                $user_lastname = $user['user_lastname'];
                $user_email = $user['user_email'];
            ?>
                <tr>
                    <td><?= $user_id ?></td>
                    <td><img style="width: 40px; height: 40px;" src="<?= $user_image ?>" alt="user image"></td>
                    <td><?= $user_name ?></td>
                    <td><?= $user_firstname ?></td>
                    <td><?= $user_lastname ?></td>
                    <td><?= $user_email ?></td>
                    <td><a href="<?= $site_url . "admin/user.php?source=edit_user&user_id=$user_id&nonces=$nonces" ?>">Edit</a> &nbsp; <a href="<?= $site_url . "admin/user.php?source=delete_user&user_id=$user_id&nonces=$nonces" ?>">Delete</a></td>
                </tr>
            <?php  } ?>
        </tbody>
    </table>
</div>