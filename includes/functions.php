<?php include_once "db.php";

function _init()
{
    global  $site_url, $site_dir, $current_url, $image_folder, $upload_image_url, $upload_image_dir, $current_page, $page_title;
    $site_url = 'http://localhost/cms/';
    $site_folder_name = explode('/', $_SERVER['SCRIPT_NAME']);
    $site_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $site_folder_name[1] . '/';
    $current_url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $image_folder = 'upload/images/';
    $upload_image_url = $site_url . $image_folder;
    $upload_image_dir = $site_dir . $image_folder;
    $current_page = _current_page();
    date_default_timezone_set('UTC');
    if (isset($_GET['source']) && !empty($_GET['source'])) {
        $page_source_id = $_GET['source'];
        $page_source_id = explode('_', $page_source_id);
        foreach ($page_source_id as $key => $page_name) {
            $page_source_id[$key] = ucfirst($page_name);
        }
        $page_title = implode(' ', $page_source_id);
    } else if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
        $page_basename = basename($_SERVER['SCRIPT_FILENAME']);
        $page_basename = explode('.', $page_basename);
        $page_title = ucfirst($page_basename[0]);
    } else $page_title = '<no title>';
}
_init();

/** 
 * @return array return multidimesion array of all categories
 **/
function _get_all_category()
{
    global $db;
    $query = "SELECT * FROM categories";
    $result = mysqli_query($db, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

/**
 * Check to see if the category id is avalible in database
 * @param int $id id of the category
 * @return true|false
 */
function _in_category($id)
{
    $categories = _get_all_category();
    $all_key = [];
    foreach ($categories as $category) {
        $all_key[] = $category['cat_id'];
    }
    return in_array($id, $all_key);
}

/**
 * @param int $id id of the category
 * @return string name of the category or false whether the category $id is avalible 
 */
function _get_category(int $id)
{
    if (_in_category($id)) {
        global $db;
        $query =  "SELECT cat_title FROM categories WHERE cat_id = ?";
        $stmt = $db->stmt_init();
        $stmt->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($category);
        while ($stmt->fetch()) {
            return $category;
        }
    }
    return false;
}

/**
 * Add new category to database
 * @param string $val
 * @return true|error message
 */
function _add_category($val)
{
    global $db;
    $query = "INSERT INTO categories ( cat_title ) VALUES ( ? )";
    //initalize the prepared statement
    $stmt = $db->stmt_init();
    if ($stmt->prepare($query)) {
        $stmt->bind_param('s', $val);
        if ($stmt->execute()) {
            return true;
        } else return $stmt->error;
    } else {
        return $stmt->error;
    }
}

/**
 * @param int $id id of category
 */
function _delete_category($id)
{
    $categories = _get_all_category();
    foreach ($categories as $category) {
        $in_category = in_array($id, $category);
    }
    if (isset($in_category)) {
        global $db;
        $query = "DELETE FROM categories WHERE cat_id = ?";
        //initalize the prepared statement
        $stmt = $db->stmt_init();
        if ($stmt->prepare($query)) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                return true;
            } else return $stmt->error;
        } else {
            return $stmt->error;
        }
    } else return false;
}
/**
 * @param int $id id of the category
 * @param string $value value to updated
 * @return true || mysqli_error
 */
function _update_category($id, $value)
{
    $categories = _get_all_category();
    foreach ($categories as $category) {
        $in_category = in_array($id, $category);
    }
    if (isset($in_category)) {
        global $db;
        $query = "UPDATE categories SET cat_title = ? WHERE cat_id = ?";
        //initalize the prepared statement
        $stmt = $db->stmt_init();
        if ($stmt->prepare($query)) {
            $stmt->bind_param('si', $value, $id);
            if ($stmt->execute()) {
                return true;
            } else return $stmt->error;
        } else {
            return $stmt->error;
        }
    } else return false;
}



/**
 * @return string $path path of current page 
 */
function _current_page()
{
    $directoryURI = $_SERVER['SCRIPT_FILENAME'];
    $path = basename($directoryURI);
    return $path;
}

function _print_r(array $value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
}

/**
 * @param string $filename filename to be return without extension
 * @return string $filename return filename without extension  
 */
function file_name_without_ext(string $filename)
{
    $new_filename = '';
    $filename = explode('.', $filename);
    $filename_count = count($filename);
    if ($filename_count >= 2) {
        for ($i = 0; $i < ($filename_count - 1); $i++) {
            $new_filename .= $filename[$i];
        }
        return $new_filename;
    }
    return false;
}

/**
 * @param string $path path of the folder 
 * @param string $filename filename of the file
 * @return string return the appropriate filename
 */
function change_file_name_if_exist(string $path, string $filename)
{
    $counter = 1;
    $file_extension = pathinfo($filename)['extension'];
    if (file_name_without_ext($filename)) {
        $filename_without_ext = file_name_without_ext($filename);
    } elseif (file_name_without_ext($filename) == false) {
        return false;
    }
    $is_exists = true;
    while ($is_exists) {
        $updated_filename = $filename_without_ext . "-$counter.$file_extension";
        if (file_exists($path . $updated_filename)) {
            $counter++;
            continue;
        } elseif (!file_exists($path . $updated_filename)) {
            $is_exists = false;
            return $updated_filename;
        }
    }
}

function _create_nonces()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $number  = rand(10, 10);
    $hash = password_hash($number, PASSWORD_DEFAULT);
    $_SESSION['nonces'] = $number;
    return $hash;
}

function _verify_nonces()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (isset($_GET['nonces']) && isset($_SESSION['nonces'])) {
        $nonces_hash =  $_GET['nonces'];
        $nonces_number = $_SESSION['nonces'];
        if (password_verify($nonces_number, $nonces_hash)) {
            return true;
        } else {
            return "verify errror";
        }
    } else {
        return "unauthorized";
    }
    return;
}

/**
 * @return array of all posts
 */
function _get_all_posts()
{
    global $db, $site_url;
    $posts = [];
    $post_query = "SELECT * FROM posts";
    $post_result = mysqli_query($db, $post_query);
    if ($post_result) {
        while ($row = mysqli_fetch_assoc($post_result)) {
            $post_permalink = $site_url . 'post.php?post_id=' . $row['post_id'];
            $row['post_permalink'] = $post_permalink;
            $posts[] = $row;
        }
        return $posts;
    } else {
        return mysqli_error($db);
    }
}

/**
 * check to see if the post id is avalible on database
 * @param int $id of the post
 */
function _is_post(int $id)
{
    $posts = _get_all_posts();
    $all_id = [];
    foreach ($posts as $post) {
        $all_id[] = $post['post_id'];
    }
    return in_array($id, $all_id);
}

/**
 * @param int $id if supplied return specific post
 * @param int $max_post maxmimum post to be displayed default is 10
 * @return array of post data which use in loop
 */
function _get_post(int $id = null, int $max_post = 10)
{
    global $db, $site_url;
    $id_true = false;
    if (isset($id) && _is_post($id)) {
        $id_true = true;
        $query = "SELECT * FROM posts WHERE post_id = ?";
    } else {
        $query = "SELECT * FROM posts";
    }
    $stmt = $db->stmt_init();
    if ($stmt->prepare($query)) {
        if ($id_true) {
            $stmt->bind_param('i', $id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) return false;
        $posts = [];
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            if ($i < $max_post) {
                if ($row['post_tag'] != '') {
                    $row['post_tag'] = unserialize($row['post_tag']);
                    $tags = '';
                    foreach ($row['post_tag'] as $tag) {
                        $tags .= $tag . ' ';
                    }
                    $row['post_tag'] = $tags;
                }
                $post_permalink = $site_url . 'post.php?post_id=' . $row['post_id'];
                $row['post_permalink'] = $post_permalink;
                $posts[] = $row;
            } else {
                break;
            }
            $i++;
        }
        return $posts;
    }
    return false;
}

function _get_post_publish_options()
{
    $post_publish_option = ['draft', 'published'];
    return $post_publish_option;
}
