<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?= $site_url ?>">Home</a>
    </div>
    <!-- Top Menu Items -->
    <ul class="nav navbar-right top-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> John Smith <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li>
                    <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
                </li>
                <li>
                    <a href=""><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                </li>
            </ul>
        </li>
    </ul>
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li <?php if( $current_page == 'index.php' ) echo 'class="active"'; ?>>
                <a href="index.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-arrows-v"></i> Posts <i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo" class="collapse">
                    <li>
                        <a href="<?= $site_url ?>admin/posts.php?source=add_post">Add Post</a>
                    </li>
                    <li>
                        <a href="<?= $site_url ?>admin/posts.php?source=view_posts">View Posts</a>
                    </li>
                </ul>
            </li>
            <li <?php if( $current_page == 'categories.php' ) echo 'class="active"'; ?>>
                <a href="categories.php"><i class="fa fa-fw fa-desktop"></i> Category</a>
            </li>
            <li <?php if( $current_page == 'comments.php' ) echo 'class="active"'; ?>>
                <a href="comments.php"><i class="fa fa-fw fa-wrench"></i> Comments</a>
            </li>
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#cat"><i class="fa fa-fw fa-arrows-v"></i> Users <i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="cat" class="collapse">
                    <li <?php if( $current_page == 'add_user.php' ) echo 'class="active"'; ?>>
                        <a href="add_user.php">Add User</a>
                    </li>
                    <li <?php if( $current_page == 'view_user.php' ) echo 'class="active"'; ?>>
                        <a href="view_user.php">View User</a>
                    </li>
                </ul>
            </li>
            <li <?php if( $current_page == 'profile.php' ) echo 'class="active"'; ?>>
                <a href="profile.php"><i class="fa fa-fw fa-dashboard"></i> Profile</a>
            </li>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>