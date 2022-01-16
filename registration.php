<?php  include "includes/db.php"; ?>
<?php  include "includes/header.php"; ?>
    <!-- Navigation -->    
<?php  include "includes/nav.php"; ?>
<?php 
if( isset( $_POST['register'] ) )  {
    $required = ['username', 'email', 'password'];
    $error = [];
    foreach( $required as $field )  {
        if( isset( $_POST[$field] ) && $_POST[$field]  != '' )  {
             $$field = $_POST[$field];
        } else {
            $error[$field] = "This field cannot be empty";
        }
    }

    if( empty( $error ) )  {
        if(!_is_username_available( $username ))  {
            $error['username'] = "Username is already taken please try another one";
        } elseif( _is_username_available( $username ) )  {
            $password = password_hash( $password, PASSWORD_DEFAULT );
            $query = "INSERT INTO users (user_role, user_name, user_email, user_password) VALUES ('subscriber', ?, ?, ?)";
            $stmt = $db->stmt_init();
            $stmt->prepare( $query );
            $stmt->bind_param( 'sss', $username, $email, $password );
            $stmt->execute();
            if( mysqli_affected_rows( $db ) > 0 )  {
                $_SESSION['user_register_success'] = true;
            } else {
                $_SESSION['user_register_success'] = $stmt->error;
            }
        }
    }
    $_SESSION['error'] = $error;
    $location = $site_url . 'registration.php';
    header( "Location: $location" );
    exit;
}

?>
    <!-- Page Content -->
    <div class="container">
    
<section id="login">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <?php 
                if( isset( $_SESSION['user_register_success'] ) && $_SESSION['user_register_success'] == true )  { ?>
                  <div class="alert alert-success">User Register Successfully</div>
               <?php } elseif( isset( $_SESSION['user_register_success'] ) && $_SESSION['user_register_success'] != true )  { ?>
                <div class="alert alert-danger"><?= $_SESSION['user_register_success'] ?></div>
             <?php  }
             unset( $_SESSION['user_register_success'] );
                ?>
                <div class="form-wrap">
                <h1 style="margin-bottom: 10px;">Register</h1>
                    <form role="form" action="registration.php" method="post" id="login-form">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <?php if( isset( $_SESSION['error']['username'] ) )  { ?>
                           <div class="alert alert-danger"><?= $_SESSION['error']['username'] ?></div>
                            <?php unset( $_SESSION['username'] ); } ?>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username">
                        </div>
                         <div class="form-group">
                            <label for="email">Email</label>
                            <?php if( isset( $_SESSION['error']['email'] ) )  { ?>
                           <div class="alert alert-danger"><?= $_SESSION['error']['email'] ?></div>
                            <?php unset( $_SESSION['email'] ); } ?>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        </div>
                         <div class="form-group">
                            <label for="password">Password</label>
                            <?php if( isset( $_SESSION['error']['password'] ) )  { ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']['password'] ?></div>
                            <?php unset( $_SESSION['password'] ); } ?>
                            <input type="password" name="password" id="key" class="form-control" placeholder="Password">
                        </div>
                
                        <input type="submit" name="register" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Register">
                    </form>
                 
                </div>
            </div> <!-- /.col-xs-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</section>
        <hr>
<?php include "includes/footer.php";?>
