<?php include_once "./includes/header.php" ?>

<div id="wrapper">

    <!-- Navigation -->
    <?php include_once "./includes/nav.php" ?>

    <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <?php 
                        if( _is_logged_in() )  { ?>
                         Welcome <?= _get_username( _get_current_user_id() ) ?>
                       <?php } ?>
                    </h1>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<!-- jQuery -->
<?php include_once "./includes/footer.php" ?>