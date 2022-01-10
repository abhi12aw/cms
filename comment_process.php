<?php 

  include_once "./includes/functions.php";
  global $site_url;
  $location = $site_url;
  if( isset( $_POST['comment_submit'] ) && ( isset( $_POST['comment_post_id'] ) && $_POST['comment_post_id'] != ''  ) )  {
     $comment_post_id = $_POST['comment_post_id'];
     $location.= "post.php?post_id=$comment_post_id" . '#comment';
     $comment_author = $_POST['comment_author'];
     $comment_email = $_POST['comment_email'];
     $comment_content = $_POST['comment_content'];
     $comment_status = 0;
     $comment_date = date('Y-d-m');
     $required_field = ['comment_author' => $comment_author, 'comment_email' => $comment_email, 'comment_content' => $comment_content];
     $comment_error = [];
     foreach( $required_field as $key => $required )  {
         if( $required == '' )  {
           $comment_error[$key] = 'This field cannot be empty';
         }
     }
     if( empty($comment_error) )  {
         $query = "INSERT INTO comments (comment_post_id, comment_author, comment_email, comment_content, comment_status, comment_date)
                  VALUES ( ?, ?, ?, ?, ?, ? )
         ";
         $stmt = $db->stmt_init();
         $stmt->prepare( $query );
         if(!$stmt->bind_param( 'isssss', $comment_post_id, $comment_author, $comment_email, $comment_content, $comment_status, $comment_date) )  {
            $comment_error['db_error'] = $stmt->error;
         }
         if( !isset($comment_error['db_error']) )  {
           if($stmt->execute())  {
              $comment_error['no_error'] = "Comment added successfully";
           } else {
               $comment_error['db_error'] = $stmt->error;
           }
         }
     } 
     if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
     }
     $_SESSION['comment_message'] = $comment_error;
 }
     header( "Location: $location" );
?>