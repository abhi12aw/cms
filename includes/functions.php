<?php include_once "db.php";

function _init()
{
    global  $site_url, $site_dir, $current_url, $image_folder, $upload_image_url, $upload_image_dir, $current_page, $page_title, $cms_image_folder_url;
    $site_url = 'http://localhost/cms/';
    $site_folder_name = explode('/', $_SERVER['SCRIPT_NAME']);
    $site_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $site_folder_name[1] . '/';
    $current_url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $image_folder = 'upload/images/';
    $upload_image_url = $site_url . $image_folder;
    $upload_image_dir = $site_dir . $image_folder;
    $current_page = _current_page();
    $cms_image_folder_url = $site_url . "images/";
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

function sanitize_op($value)
{
    return trim(htmlentities($value, ENT_QUOTES));
}

/**
 * start the session if already not statred
 * @return void
 */
function _session_start()  {
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
}

/** 
 * @return array return multidimesion array of all categories
 **/
function _get_all_category()
{
    global $db, $site_url;
    $query = "SELECT * FROM categories";
    $result = mysqli_query($db, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $category_permalink = $site_url . "categories.php?cat_id={$row['cat_id']}";
        $row['cat_permalink'] = $category_permalink;
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

/**
 * verify the nonces
 * @return true|error message 
 */
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
            $row['post_excerpt'] = substr($row['post_content'], 0, 150) . '.....';
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
                $row['post_excerpt'] = substr($row['post_content'], 0, 150) . '.....';
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

/**
 * @param array array of args if given show the comment of the post args post_id, comment_id
 * @param bool whether we want a approve comments default is true
 * @return array of comments
 */
function _get_comments($args = '', bool $get_approve_comments = true)
{
    global $db;
    $comments = [];
    $query = "SELECT * FROM comments";
    if (isset($args) && !empty($args)) {
        if (!empty($args['comment_id']) || !empty($args['post_id'])) {
            $query .= " WHERE ";
        }
        if (!empty($args['comment_id'])) {
            $query .= " comment_id = ? ";
            if (!empty($args['comment_id']) && !empty($args['post_id'])) {
                $query .= " AND ";
            }
        }
        if (!empty($args['post_id'])) {
            $query .= " comment_post_id = ? ";
        }
    }
    $stmt = $db->stmt_init();
    $stmt->prepare($query);
    if (!empty($args['comment_id']) && !empty($args['post_id'])) {
        $stmt->bind_param('ii', $args['comment_id'], $args['post_id']);
    } elseif (!empty($args['comment_id'])) {
        $stmt->bind_param('i', $args['comment_id']);
    } elseif (!empty($args['post_id'])) {
        $stmt->bind_param('i', $args['post_id']);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = mysqli_fetch_assoc($result)) {
        if ($get_approve_comments == true && $row['comment_status'] != 1) {
            continue;
        }
        $comments[] = $row;
    }
    return $comments;
}

/**
 * @param int $id of the comment
 * @param true|false whether we want a approve comments default is true
 * @return true|false if the id is in the database
 */
function _is_comment(int $id, $get_approve_comments = true)
{
    $comment = _get_comments($id, $get_approve_comments);
    if (!empty($comment)) {
        return true;
    }
    return false;
}

/**
 * this function can update comment count on the post database table
 * only approve comment are shown in the count
 * @param int $post_id of the post to be updated
 * @return void
 */
function comment_count_update(int $post_id)
{
    if (_is_post($post_id)) {
        global $db;
        $all_comments = _get_comments(['post_id' => $post_id], true);
        $comments_count = count($all_comments);
        $query = "UPDATE posts SET post_comment_count = '$comments_count' WHERE post_id = '$post_id'";
        mysqli_query($db, $query);
    }
}


/**
 * get the user
 * @param int $id if provided get sepecfic user unless all the users
 * @return array of all the user
 */
function _get_users( int $id = null )
{
    global $db;
    $query = "SELECT * FROM users";
    if( isset( $id ) )  {
        $query.= " WHERE user_id = $id";
    }
    $result = mysqli_query($db, $query);
    $all_users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $all_users[] = $row;
    }
    return $all_users;
}


/**
 * check to see if username is avalible or not 
 * @param mixed $username
 * @return true|false
 */

function _is_username_available($username)
{
    global $db;
    $query = "SELECT user_name FROM users";
    $result = mysqli_query($db, $query);
    $all_username = [];
    while ($row = mysqli_fetch_row($result)) {
        $all_username[] = $row;
    }
    foreach ($all_username as $user) {
        if ($user[0] == $username) {
            return false;
        }
    }
    return true;
}

/**
 * check if the user id avaliable
 * @param int $id
 * @return true|false
 */
function _is_user(int $id)
{
    global $db;
    $query = "SELECT user_id FROM users";
    $result = mysqli_query($db, $query);
    $count = mysqli_num_rows($result);
    while ($row = mysqli_fetch_row($result)) {
        if ($row[0] == $id) {
            return true;
        }
    }
    return false;
}

/**
 * get the username
 * @param int $id of the user
 * @return string|false username or false
 */
function _get_username( int $id )  {
    if( !_is_user( $id ) ) return false;
    global $db;
    $query = "SELECT user_name FROM users WHERE user_id = ?";
    $stmt = $db->stmt_init();
    $stmt->prepare( $query );
    $stmt->bind_param( 'i', $id );
    $stmt->execute();
    $result = $stmt->get_result();
    $username = '';
    while ($row = mysqli_fetch_row($result)) {
        $username = $row[0];
    }
    return $username;
}

/**
 * check if logged in 
 */
function _is_logged_in()  {
    if( isset( $_SESSION['login'] ) && $_SESSION['login'] == true )  {
        return true;
    }
    return false;
}

function _get_current_user_id()  {
    if( _is_logged_in() )  {
      if( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] != '' )  {
          return $_SESSION['user_id'];
      }
    }
    return false;
}