<?php include_once "./includes/header.php"; 
define( 'COMMENT_PAGE_PART', '' );
?>

<div id="wrapper">

    <!-- Navigation -->
    <?php include_once "./includes/nav.php" ?>

    <div id="page-wrapper" style="border-radius: 7px; margin: 65px 10px 10px 10px; width: auto; padding: 25px 10px;">
        <div class="container-fluid">
            <?php  
             if( isset($_GET['source']) )  {
                 $source = $_GET['source'];
                 switch( $source )  {
                     case 'delete_comment':
                        include_once  "delete_comment.php";
                        break;
                    case 'approve_comment':
                        include_once  "approve_comment.php";
                        break;
                    case 'unapprove_comment':
                        include_once "unapprove_comment.php";
                        break;
                 }
             } elseif( !isset($_GET['source']) )  {
                include_once  "view_comments.php";
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