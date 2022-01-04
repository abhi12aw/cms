<?php include_once "./includes/header.php" ?>
<?php
///Category add
if (isset($_POST['add_category'])) {
    $category = trim($_POST['cat_title']);
    if (!empty($category)) {
        if (_add_category($category) == true) {
            $message = "<h3 class='text-success'>Category added successfully</h3>";
        } else {
            $message = "<h3 class='text-danger'>" . _add_category($category) . "<h3>";
        }
    } else if (empty($category)) {
        $message = "<h3 class='text-danger'>Category name should not be empty<h3>";
    } else $message = "<h3 class='text-danger'>Something went wrong<h3>";
}
///Category delete
if (isset($_GET['del_cat_id']) && !empty($_GET['del_cat_id'])) {
    if (_delete_category($_GET['del_cat_id']) == true) {
        $message = "<h3 class='text-danger'>Category deleted successfully<h3>";
    } else echo $message = "<h3 class='text-danger'>Something went wrong<h3>";
}
//Category update
if( isset( $_POST['update_category'] ) )  {
  if( !empty( $_POST['rename_category_id'] ) && !empty( $_POST['category_update_name'] ) )  {
     $category_update_id = $_POST['rename_category_id'];
     $category_update_value = trim($_POST['category_update_name']);
     $is_updated = _update_category($category_update_id, $category_update_value);
     if( $is_updated == true )  {
        $message = "<h3 class='text-success'>Category updated successfully</h3>";
     } else {
        $message = "<h3 class='text-danger'>Something went wrong<h3>";
     }
  } else if( empty( $_POST['rename_category_id'] ) || empty( $_POST['category_update_name'] ) )  $message = "<h3 class='text-danger'>Category name should not be empty<h3>";
}
?>
<div id="wrapper">

    <!-- Navigation -->
    <?php include_once "./includes/nav.php" ?>

    <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Welcome John
                    </h1>
                </div>
            </div>
            <!-- /.row -->

            <div class="col-lg-6">
                <form action="./categories.php" method="post">
                    <div class="form-group">
                        <label for="category-name">Category Name</label>
                        <input class="form-control" type="text" name="cat_title" id="category-name">
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="add_category" value="Add Category">
                    </div>
                </form>
                <hr>
                <form action="./categories.php" method="post">
                    <div class="form-group">
                        <label for="update_category_name">Change Category Name</label>
                        <input class="form-control" type="text" name="category_update_name" id="update_category_name">
                    </div>
                    <div class="form-group">
                        <label for="rename_category_id">Rename Category</label>
                        <select class="form-control" name="rename_category_id" id="rename_category_id">
                        <?php
                        $categories = _get_all_category();
                        foreach ($categories as $category) { ?>
                            <option value="<?= $category['cat_id'] ?>"><?= $category['cat_title'] ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary" type="submit" name="update_category" value="Update Category">
                    </div>
                </form>
                <hr>
                <?php if (isset($message)) echo $message; ?>
            </div>
            <div class="col-lg-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($categories as $category) { ?>
                            <tr>
                                <td><?= $category['cat_id'] ?></td>
                                <td><?= $category['cat_title'] ?></td>
                                <td><a data-toggle="confirmation" name="" href="<?= $current_page ?>?del_cat_id=<?= $category['cat_id'] ?>">Delete</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<!-- jQuery -->
<?php include_once "./includes/footer.php" ?>