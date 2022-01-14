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
                        if (_is_logged_in()) { ?>
                            Welcome <?= _get_username(_get_current_user_id()) ?>
                        <?php }
                        //posts count 
                        $post_query = "SELECT * FROM posts WHERE post_status = 'published'";
                        $post_result = mysqli_query($db, $post_query);
                        $post_count = mysqli_num_rows($post_result);
                        //draft 
                        $post_draft_query =  "SELECT * FROM posts WHERE post_status = 'draft'";
                        $post_draft_result = mysqli_query( $db, $post_draft_query );
                        $post_draft_count = mysqli_num_rows( $post_draft_result );

                        //comments count
                        $comment_query = "SELECT * FROM comments WHERE comment_status = 1";
                        $comment_result = mysqli_query($db, $comment_query);
                        $comment_count = mysqli_num_rows($comment_result);
                        //unapproved
                        $comment_unapproved_query = "SELECT * FROM comments WHERE comment_status = 0";
                        $comment_unapproved_result = mysqli_query( $db, $comment_unapproved_query );
                        $comment_unapproved_count = mysqli_num_rows( $comment_unapproved_result ); 

                        //user count
                        $user_query = "SELECT * FROM users";
                        $user_result = mysqli_query($db, $user_query);
                        $user_count = mysqli_num_rows($user_result);

                        //category count
                        $category_query = "SELECT * FROM categories";
                        $category_result = mysqli_query($db, $category_query);
                        $category_count = mysqli_num_rows($category_result);
                        ?>
                    </h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-file-text fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Published Posts</div>
                                    <div class='huge'><?= $post_count ?></div>
                                    <div><?php if( $post_draft_count ) echo "Draft  $post_draft_count"; ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="posts.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-comments fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Approve Comments</div>
                                    <div class='huge'><?= $comment_count ?></div>
                                    <div><?php if( $comment_unapproved_count ) echo "Unapprove  $comment_unapproved_count"; ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="comments.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-user fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div> Users</div>
                                    <div class='huge'><?= $user_count ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="user.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-list fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div>Categories</div>
                                    <div class='huge'><?= $category_count ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="categories.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <script type="text/javascript">
                    google.charts.load('current', {
                        'packages': ['bar']
                    });
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Data', 'Count'],
                            <?php 
                            $data = ['Posts', 'Comments', 'Users', 'Categories'];
                            $value = [$post_count, $comment_count, $user_count, $category_count];
                            for( $i = 0; $i < count( $data ); $i++ )  {
                                echo "['{$data[$i]}', $value[$i]],";
                            }
                            ?>
                            
                        ]);

                        var options = {
                            chart: {
                                title: 'Data Count',
                            }
                        };

                        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

                        chart.draw(data, google.charts.Bar.convertOptions(options));
                    }
                </script>

                <div id="columnchart_material" style="width: auto; height: 500px; padding: 20px;"></div>

            </div>
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<!-- jQuery -->
<?php include_once "./includes/footer.php" ?>