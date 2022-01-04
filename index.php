<?php include_once 'includes/header.php' ?>

<!-- Navigation -->
<?php include_once 'includes/nav.php' ?>


<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">
            
            <h1 class="page-header">
                Page Heading
                <small>Secondary Text</small>
            </h1>

            <!-- First Blog Post -->
            <?php
            if (empty($_GET['search'])) {
                $query = "SELECT * FROM posts";
            } else if (!empty($_GET['search'])) {
                $search_query = $_GET['search'];
                $query = "SELECT * FROM posts WHERE post_tag LIKE '%$search_query%'";
            }
            $posts_result = mysqli_query($db, $query);
            if (!$posts_result) {
                echo mysqli_error($db);
            } else if ($posts_result) {
                $counter = mysqli_num_rows($posts_result);
                if ($counter == 0) {
                    echo "<h1>No Post Found</h2>";
                } else if ($counter >= 1) {
                    while ($row = mysqli_fetch_assoc($posts_result)) {
                        $post_title = $row['post_title'];
                        $post_author = $row['post_author'];
                        $post_date = $row['post_date'];
                        $post_content = $row['post_content'];
                        $post_image = $row['post_image'];
                        $post_tag = $row['post_tag'];
                        $post_comment = $row['post_comment_count'];
                        $post_status =  $row['post_status'];
                        $counter++;
            ?>
                        <h2>
                            <a href="#"><?= $post_title ?></a>
                        </h2>
                        <p class="lead">
                            by <a href="#"><?= $post_author ?></a>
                        </p>
                        <p><span class="glyphicon glyphicon-time"></span> Posted on <?= $post_date ?></p>
                        <hr>
                        <img style="width: 100%; object-fit: cover;" class="img-responsive" src="<?= $upload_image_url . trim(htmlentities($post_image, ENT_QUOTES)) ?>" alt="image">
                        <hr>
                        <p><?= $post_content ?></p>
                        <a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

                        <hr>
            <?php }
                }
            }
            ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include_once 'includes/sidebar.php' ?>


    </div>
    <!-- /.row -->

    <hr>


    <?php include_once 'includes/footer.php' ?>