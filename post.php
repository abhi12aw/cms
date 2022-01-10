<?php include_once 'includes/header.php' ?>

<!-- Navigation -->
<?php include_once 'includes/nav.php' ?>

<!-- Page Content -->
<div class="container">

    <div class="row">
        <?php
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

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
                <?php if (isset($_GET['post_id']) && $_GET['post_id'] != '') {
                    $post_id =  $_GET['post_id']; ?>
                    <div class="well" id="comment">
                        <?php
                        if (isset($_SESSION['comment_message']) && isset($_SESSION['comment_message']['no_error'])) { ?>
                            <div class="alert alert-success" role="alert"><?= $_SESSION['comment_message']['no_error'] ?></div>
                        <?php
                            unset($_SESSION['comment_message']['no_error']);
                        } ?>
                        <h4 style="margin-bottom: 25px;">Leave a Comment:</h4>
                        <form action="./comment_process.php" method="post" role="form">
                            <input type="hidden" name="comment_post_id" value="<?= $post_id ?>">
                            <div class="form-group">
                                <label for="author">Name</label>
                                <?php
                                if (isset($_SESSION['comment_message']) && isset($_SESSION['comment_message']['comment_author'])) { ?>
                                    <div class="alert alert-danger" role="alert"><?= $_SESSION['comment_message']['comment_author'] ?></div>
                                <?php
                                    unset($_SESSION['comment_message']['no_author']);
                                } ?>
                                <input class="form-control" type="text" name="comment_author" id="author" rows="3" value="<?php if (isset($_POST['comment_author'])) echo $_POST['comment_author'] ?>"></input>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <?php
                                if (isset($_SESSION['comment_message']) && isset($_SESSION['comment_message']['comment_email'])) { ?>
                                    <div class="alert alert-danger" role="alert"><?= $_SESSION['comment_message']['comment_email'] ?></div>
                                <?php
                                    unset($_SESSION['comment_message']['comment_email']);
                                } ?>
                                <input class="form-control" type="email" name="comment_email" id="email" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="content">Comment</label>
                                <?php
                                if (isset($_SESSION['comment_message']) && isset($_SESSION['comment_message']['comment_content'])) { ?>
                                    <div class="alert alert-danger" role="alert"><?= $_SESSION['comment_message']['comment_content'] ?></div>
                                <?php
                                    unset($_SESSION['comment_message']['comment_content']);
                                } ?>
                                <textarea class="form-control" name="comment_content" id="content" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" name="comment_submit">Submit</button>
                        </form>
                    </div>
                    <?php
                    $post_comment = _get_comments(["post_id" => $post_id]);
                    foreach ($post_comment as $comment) {
                        $post_comment_author = $comment['comment_author'];
                        $post_comment_email = $comment['comment_email'];
                        $post_comment_date = $comment['comment_date'];
                        $post_comment_content = $comment['comment_content'];
                    ?>
                        <div class="media">
                            <a class="pull-left" href="mailto:<?= $post_comment_email ?>">
                                <img style="width: 45px; height: 45px; object-fit: cover;" class="media-object" src="<?= $cms_image_folder_url . 'placeholder.jpg' ?>" alt="author image">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading"><?= $post_comment_author ?>
                                    <small><?= $post_comment_date ?></small>
                                </h4>
                                <?= $post_comment_content ?>
                            </div>
                        </div>
                    <?php } ?>

            <?php }
            } ?>
        </div>

        <?php include_once 'includes/sidebar.php' ?>
    </div>

    <hr>

    <?php include_once 'includes/footer.php' ?>