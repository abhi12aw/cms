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
    $nonces = _create_nonces();
    ///user id 
    $user_id = $_GET['user_id'];
    //current user data
    $current_information = _get_users($user_id);
    $current_information = $current_information[0];
    $current_username = $current_information['user_name'];
    $current_firstname = $current_information['user_firstname'];
    $current_lastname = $current_information['user_lastname'];
    $current_email = $current_information['user_email'];
    // _print_r($current_information);

    //edit user data
    if (isset($_POST['edit_user'])) {
        $username = $_POST['username'];
        $firstName = $_POST['firstname'];
        $lastName = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $conf_password = $_POST['password-conf'];
        $error = [];
        $required  = ['username' => $username, 'email' => $email];
        foreach ($required as $key => $field) {
            if (empty($field)) {
                $error[$key] =  "This cannot be field is empty";
            }
        }
        if ($password != '' && $password != $conf_password) {
            $error['password'] = "Password do not match";
        } elseif (!isset($error['password']) && $password != '' && $password == $conf_password) {
            $password = password_hash($password, PASSWORD_DEFAULT);
        }
        if (!isset($error['username']) && (!_is_username_available($username))) {
            $error['username'] = "username already taken please try another one";
        }
        if (empty($error)) {
            $add_query = "UPDATE users SET user_name = ?, user_firstname = ?, user_lastname = ?, user_email = ?";
            $stmt = $db->stmt_init();
            if ($password != '') {
                $add_query .= " user_password = ?";
                $add_query .= " WHERE user_id = ?";
                $stmt->prepare($add_query);
                $stmt->bind_param('sssssi', $username, $firstName, $lastName, $email, $password, $user_id);
            } else {
                $add_query .= " WHERE user_id = ?";
                $stmt->prepare($add_query);
                $stmt->bind_param('ssssi', $username, $firstName, $lastName, $email, $user_id);
            }
            $stmt->execute();
            if (mysqli_affected_rows($db) > 0) {
                if (session_status() != PHP_SESSION_ACTIVE) {
                    session_start();
                }
                $_SESSION['edit_success'] = true;
            }
            $location = $site_url . "admin/user.php?source=edit_user&user_id=$user_id&nonces=$nonces";
            header("Location: $location");
            exit;
        }
    }
?>

    <form action="<?= $site_url . "admin/user.php?source=edit_user&user_id=$user_id&nonces=$nonces" ?>" method="post" enctype="multipart/form-data">
        <?php
        if (isset($_SESSION['edit_success'])) { ?>
            <div class="alert alert-success"><?= "User updated successfully" ?></div>
        <?php unset($_SESSION['edit_success']);
        } ?>
        <div class="form-group">
            <label for="username">Username</label>
            <?php if (isset($error['username'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error['username'] ?>
                </div>
            <?php } ?>
            <input class="form-control" type="text" name="username" id="username" value="<?= sanitize_op($current_username) ?>">
        </div>
        <div class="form-group">
            <label for="firstname">Firstname</label>
            <input class="form-control" type="text" name="firstname" id="firstname" value="<?= sanitize_op($current_firstname) ?>">
        </div>
        <div class="form-group">
            <label for="lastname">Lastname</label>
            <input class="form-control" type="text" name="lastname" id="lastname" value="<?= sanitize_op($current_lastname) ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <?php if (isset($error['email'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error['email'] ?>
                </div>
            <?php } ?>
            <input class="form-control" type="email" name="email" id="email" value="<?= sanitize_op($current_email) ?>">
        </div>
        <div class="form-group">
            <label for="password">Change Password</label>
            <?php if (isset($error['password'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error['password'] ?>
                </div>
            <?php } ?>
            <input class="form-control" type="password" name="password" id="password">
        </div>
        <div class="form-group">
            <label for="password-conf">Confirm Password</label>
            <?php if (isset($error['password'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error['password'] ?>
                </div>
            <?php } ?>
            <input class="form-control" type="password" name="password-conf" id="password-conf">
        </div>
        <input class="btn btn-primary" value="Update user" name="edit_user" type="submit">
    </form>

<?php } ?>