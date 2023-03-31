<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
  exit; // exit the script to prevent further output
}

$db = new PDO('sqlite:database.db');

if (isset($_GET['id'])) {
  $post_id = $_GET['id'];
  $query = "SELECT * FROM posts WHERE id='$post_id'";
  $post = $db->query($query)->fetch();
  
  if ($post) {
    $query = "DELETE FROM posts WHERE id='$post_id'";
    $db->exec($query);
    
    $cover_path = "cover/{$post['cover']}";
    if (file_exists($cover_path) && is_file($cover_path)) {
      unlink($cover_path);
    }
  }
}

header('Location: profile.php');
exit; // exit the script to prevent further output
?>
