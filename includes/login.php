<?php
include_once "header.php";
include_once 'nav.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $error = [];

    $query = "SELECT * FROM users WHERE user_name = ?";
    $stmt = $db->stmt_init();
    $stmt->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if (mysqli_num_rows($result) > 0) {
        $userdata = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $userdata = $row;
        }
        if( $password == '' )  {
            $error['password'] = "This is field is empty";
        }
        if( $password != '' )   {
            if( password_verify( $password, $userdata['user_password'] ) )  {
                _session_start();
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $userdata['user_id'];
                $location =  $site_url . "admin/index.php";
                header("Location: $location");
                exit;
            } elseif( !password_verify( $password, $userdata['user_password'] ) )  {
                $error['password'] = "The given password is wrong";
            }
        }
    } elseif (mysqli_num_rows($result) == 0) {
        $error['username'] = "username does not exists";
    }
  

} ?>
<div class="container">
    <?php if( isset($ok) && $ok )  { ?>
        <div class="alert alert-success">Login successfully</div>
    <?php } ?>
    <?php if( isset($_SESSION['login_warning']) && $_SESSION['login_warning'] )  { ?>
        <div class="alert alert-warning">You must loggin first</div>
    <?php unset( $_SESSION['login_warning'] ); } ?>
    <div class="well">
        <h3 class="text-center">Login</h3>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <?php if (isset($error['username'])) { ?>
                    <div class="alert alert-danger">
                        <?= $error['username'] ?>
                    </div>
                <?php  } ?>
                <input class="form-control" type="text" name="username" id="username" value="<?php if (isset($_POST['username'])) echo sanitize_op($_POST['username']) ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <?php if (isset($error['password'])) { ?>
                    <div class="alert alert-danger">
                        <?= $error['password'] ?>
                    </div>
                <?php  } ?>
                <input class="form-control" type="password" name="password" id="password">
            </div>
            <input style="margin-top: 15px;" class="btn btn-primary" name="login" value="Login" type="submit">
        </form>
        <!-- /.input-group -->
    </div>
    <?php include_once 'footer.php' ?>