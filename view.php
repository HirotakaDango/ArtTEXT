<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];
$query = "SELECT posts.id, posts.title, posts.synopsis, posts.content, posts.user_id, posts.tags, posts.date, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = '$id'";
$post = $db->query($query)->fetch();

// Query for the next and previous posts by the same user
$next_post_query = "SELECT id FROM posts WHERE user_id = '$post[user_id]' AND id > '$id' ORDER BY id ASC LIMIT 1";
$next_post = $db->query($next_post_query)->fetch();
$previous_post_query = "SELECT id FROM posts WHERE user_id = '$post[user_id]' AND id < '$id' ORDER BY id DESC LIMIT 1";
$previous_post = $db->query($previous_post_query)->fetch();

// Query to check if there are more than 1 post by the same user
$user_posts_query = "SELECT id FROM posts WHERE user_id = '$post[user_id]'";
$user_posts = $db->query($user_posts_query)->fetchAll();

$theme = 'light';
if(isset($_COOKIE['theme'])) {
  $theme = $_COOKIE['theme'];
} else if(isset($_SERVER['HTTP_REFERER'])) {
  $prev_page = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
  if(in_array($prev_page, ['/index.php', '/upload.php', '/profile.php', '/edit.php', '/setting.php', '/view.php', '/session.php'])) {
    if(isset($_SESSION['theme'])) {
      $theme = $_SESSION['theme'];
    }
  }
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $theme = $_POST['theme'];
  setcookie('theme', $theme, time() + (86400 * 30), "/");
  $_SESSION['theme'] = $theme;
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en" <?php if($theme == 'dark') { echo 'data-bs-theme="dark"'; } ?>>
  <head>
    <title><?php echo $post['title'] ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php include('header.php'); ?>
    <div class="container fw-bold mt-5">
      <h1 class="text-center fw-semibold"><?php echo isset($post['title']) ? $post['title'] : '' ?></h1>
      <p class="mt-5">Author: <?php echo isset($post['username']) ? $post['username'] : '' ?></p>
      <p class="mt-2">Published: <?php echo isset($post['date']) ? $post['date'] : '' ?></p>
      <p class="mt-2">Genre: <?php echo isset($post['tags']) ? $post['tags'] : '' ?></p>
      <p class="mt-3">Synopsis</p> 
      <small class="container font-l"><?php echo isset($post['synopsis']) ? $post['synopsis'] : '' ?></small> 
      <hr>
      <small class="mt-3 font-l" style="word-wrap: break-word;"><?php echo isset($post['content']) ? $post['content'] : '' ?></small></br>
      <div class="mb-5"></div>
      <?php if ($next_post && isset($next_post['id'])): ?>
        <a class="text-decoration-none float-start mb-5 btn btn-primary rounded-pill btn-sm fw-bold" href="view.php?id=<?php echo $next_post['id'] ?>"><i class="bi-arrow-left-circle-fill"></i> Next</a>
      <?php endif; ?> 
      <?php if ($previous_post && isset($previous_post['id'])): ?>
        <a class="text-decoration-none float-end mb-5 btn btn-primary rounded-pill btn-sm fw-bold" href="view.php?id=<?php echo $previous_post['id'] ?>">Previous <i class="bi bi-arrow-right-circle-fill"></i></a>
      <?php endif; ?>
      <br>
    </div>
    <style>
      @media (min-width: 768px) {
        .font-l {
          font-size: 17px;
        }
      }  
    </style>
  </body>
</html>
