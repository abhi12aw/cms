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

<?php 
if( isset( $_POST['add_user'] ) )  {
    $username = $_POST['username'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $conf_password = $_POST['password-conf'];
    $error = [];
    $required  = ['username' => $username, 'password' => $password, 'email' => $email];
    $all_field = [$username, $firstName, $lastName, $email, $password, $conf_password];
    foreach( $required as $key => $field )  {
        if( empty( $field ) )  {
            $error[$key] =  "This cannot be field is empty";
        }
    }
    if( !isset( $error['password'] ) && $password != $conf_password )  {
       $error['password'] = "Password do not match";
    }
    if( !isset( $error['username'] ) && ( !_is_username_available( $username ) ) )  {
        $error['username'] = "username already taken please try another one";
    }
    if( empty( $error ) )  {
        $password_hash = password_hash( $password, PASSWORD_DEFAULT );
        $add_query = "INSERT INTO users ( user_name, user_password, user_firstname, user_lastname, user_email ) VALUES ( ?, ?, ?, ?, ? )";
        $stmt = $db->stmt_init();
        if( !$stmt->prepare( $add_query ) )  {
          $error['mysql'] = $stmt->error;
        }
        $stmt->bind_param( 'sssss' ,$username, $password_hash, $firstName, $lastName, $email );
        if( $stmt->execute() )  {
            $error['no_error'] = "User added successfully";
        } else {
            $error['mysql'] = $stmt->error;
        }
    }
}

?>

<form action="<?= $site_url . 'admin/user.php?source=add_user' ?>" method="post" enctype="multipart/form-data">
<?php 
if( isset( $error['no_error'] ) )  { ?>
<div class="alert alert-success"><?= $error['no_error'] ?></div>
<?php } ?>
    <div class="form-group">
        <label for="username">Username</label>
        <?php if( isset( $error['username'] ) ) { ?>
            <div class="alert alert-danger" role="alert">
           <?= $error['username'] ?>
        </div>
        <?php } ?>
        <input class="form-control" type="text" name="username" id="username"
        <?php if( isset( $_POST['username'] ) ) echo "value='". sanitize_op( $_POST['username'] ) . "'"; ?>>
    </div>
    <div class="form-group">
        <label for="firstname">Firstname</label>
        <input class="form-control" type="text" name="firstname" id="firstname" <?php if( isset( $_POST['firstname'] ) ) echo "value='". sanitize_op( $_POST['firstname'] ) . "'"; ?>>
    </div>
    <div class="form-group">
        <label for="lastname">Lastname</label>
        <input class="form-control" type="text" name="lastname" id="lastname"<?php if( isset( $_POST['lastname'] ) ) echo "value='". sanitize_op( $_POST['lastname'] ) . "'"; ?>>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <?php if( isset( $error['email'] ) ) { ?>
            <div class="alert alert-danger" role="alert">
           <?= $error['email'] ?>
        </div>
        <?php } ?>
        <input class="form-control" type="email" name="email" id="email" <?php if( isset( $_POST['email'] ) ) echo "value='". sanitize_op( $_POST['email'] ) . "'"; ?>>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <?php if( isset( $error['password'] ) ) { ?>
            <div class="alert alert-danger" role="alert">
           <?= $error['password'] ?>
        </div>
        <?php } ?>
        <input class="form-control" type="password" name="password" id="password">
    </div>
    <div class="form-group">
        <label for="password-conf">Confirm Password</label>
        <?php if( isset( $error['password'] ) ) { ?>
            <div class="alert alert-danger" role="alert">
           <?= $error['password'] ?>
        </div>
        <?php } ?>
        <input class="form-control" type="password" name="password-conf" id="password-conf">
    </div>
    <input class="btn btn-primary" value="Add user" name="add_user" type="submit">
</form>