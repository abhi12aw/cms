<?php include_once 'includes/header.php' ?>

<!-- Navigation -->
<?php include_once 'includes/nav.php' ?>

<!-- Page Content -->
<div class="container">

    <div class="row">
        <?php
        if (isset($_GET['post_id']) && $_GET['post_id'] != '') {
            $post_id =  $_GET['post_id'];
            $posts = _get_post($post_id);
        } else {
            $posts = _get_post();
        }
        ?>
        <!-- Blog Post Content Column -->
        <div class="col-lg-8">

            <?php foreach ($posts as $post) {  ?>
                <!-- Title -->
                <h1><?= trim(htmlentities($post['post_title'], ENT_QUOTES)) ?></h1>
                <!-- Author -->
                <p class="lead">
                    by <io><?= trim(htmlentities($post['post_author'], ENT_QUOTES))  ?></io>
                </p>
                <hr>
                <!-- Date/Time -->
                <p><span class="glyphicon glyphicon-time"></span> Posted on <?= trim(htmlentities($post['post_date'], ENT_QUOTES)) ?></p>
                <hr>
                <!-- Preview Image -->
                <img class="img-responsive" src="<?= trim(htmlentities($upload_image_url . $post['post_image'], ENT_QUOTES)) ?>" alt="">
                <hr>
                <!-- Post Content -->
                <p class="lead"><?= trim(htmlentities($post['post_content'], ENT_QUOTES)) ?></p>
                <hr>
            <?php } ?>
        </div>

        <?php include_once 'includes/sidebar.php' ?>
    </div>

    <hr>

    <?php include_once 'includes/footer.php' ?>