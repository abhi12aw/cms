<?php
include_once "./includes/header.php";
define('USER_PAGE_PART', '');
?>
<div id="wrapper">

    <!-- Navigation -->
    <?php include_once "./includes/nav.php" ?>

    <div id="page-wrapper" style="border-radius: 7px; margin: 65px 10px 10px 10px; width: auto; padding: 25px 10px;">
        <div class="container-fluid">

            <?php
            if (isset($_GET['source'])) {
                switch ($_GET['source']) {
                    case 'add_user':
                        include_once "add_user.php";
                        break;
                    case 'edit_user':
                        include_once "edit_user.php";
                        break;
                    case 'delete_user':
                        include_once "delete_user.php";
                        break;
                    case 'view_users':
                    default:
                        include_once "view_users.php";
                        break;
                }
            } else {
                include_once "view_users.php";
            }
            ?>

        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<!-- jQuery -->
<?php include_once "./includes/footer.php" ?>