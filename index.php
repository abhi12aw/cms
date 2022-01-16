<?php include_once 'includes/header.php' ?>

<!-- Navigation -->
<?php include_once 'includes/nav.php' ?>


<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <!-- First Blog Post -->
            <?php
            ///post limit
            $post_limit = 2;
            $post_page = 0;
            if( isset( $_GET['page'] ) && $_GET['page'] > 0 && is_numeric( $_GET['page'] ))  {
              $post_page =  $_GET['page'];
            }
            $post_from = (($post_page * $post_limit) - $post_limit);
            if( $post_from < 0 )  {
                $post_from = 0;
            }
            if (empty($_GET['search'])) {
                $query = "SELECT * FROM posts WHERE post_status = 'published' LIMIT $post_from, $post_limit";
                $total_post_count_query =  "SELECT * FROM posts WHERE post_status = 'published'";
            } else if (!empty($_GET['search'])) {
                $search_query = $_GET['search'];
                $query = "SELECT * FROM posts WHERE post_tag LIKE '%$search_query%' AND post_status = 'published' LIMIT $post_from, $post_limit ";
                $total_post_count_query = "SELECT * FROM posts WHERE post_tag LIKE '%$search_query%' AND post_status = 'published'";
            }
            $total_post_count_result = mysqli_query( $db ,$total_post_count_query );
            $posts_result = mysqli_query($db, $query);
            $post_count = mysqli_num_rows($total_post_count_result);
            $post_count = ceil($post_count / $post_limit);
            echo $post_count;
            if (!$posts_result) {
                echo mysqli_error($db);
            } else if ($posts_result) {
                $counter = mysqli_num_rows($posts_result);
                if ($counter == 0  && !empty($_GET['search'])) {
                    echo "<h1>No Post Found</h2>";
                } else if ($counter >= 1) {
                    while ($row = mysqli_fetch_assoc($posts_result)) {
                        $post_id = $row['post_id'];
                        $post_title = $row['post_title'];
                        $post_author = $row['post_author'];
                        $post_date = $row['post_date'];
                        $post_content = $row['post_content'];
                        $post_image = $row['post_image'];
                        $post_tag = $row['post_tag'];
                        $post_comment = $row['post_comment_count'];
                        $post_status =  $row['post_status'];
                        $post_premalink = $site_url . "post.php?post_id=$post_id";
                        $counter++;
            ?>
                        <h2>
                            <a href="<?= $post_premalink ?>"><?= trim(htmlentities($post_title, ENT_QUOTES))  ?></a>
                        </h2>
                        <p class="lead">
                            by <io><?= $post_author ?></io>
                        </p>
                        <p><span class="glyphicon glyphicon-time"></span> Posted on <?= trim(htmlentities($post_date, ENT_QUOTES)) ?></p>
                        <hr>
                        <a href="<?= $post_premalink ?>"><img style="width: 100%; object-fit: cover;" class="img-responsive" src="<?= trim(htmlentities($upload_image_url . $post_image, ENT_QUOTES)) ?>" alt="image"></a>
                        <hr>
                        <p><?= html_entity_decode($post_content, ENT_QUOTES) ?></p>
                        <a class="btn btn-primary" href="<?= $post_premalink ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

                        <hr>
            <?php }
                }
            }
            ?>
            <?php if (!($post_count <= 1)) { ?>
                <ul class="pagination">
                    <?php
                    for ($i = 2; $i <= $post_count; $i++) { ?>
                        <li><a href="<?= _current_page() . "?page=$i" ?>"><?= $i ?></a></li>
                    <?php } ?>
                </ul>
            <?php } ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include_once 'includes/sidebar.php' ?>


    </div>
    <!-- /.row -->

    <hr>


    <?php include_once 'includes/footer.php' ?>