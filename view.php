<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];
$query = "SELECT posts.id, posts.title, posts.synopsis, posts.content, posts.user_id, posts.cover, posts.tags, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = '$id' AND posts.user_id = '$user_id'";
$post = $db->query($query)->fetch();

// Extract genres from tags column
$genres = explode(",", $post['tags']);
?>

<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $post['title'] ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php include('header.php'); ?>
    <div class="container fw-bold mt-3">
      <h1 class="text-center fw-semibold"><?php echo $post['title'] ?></h1>
      <p class="text-secondary mt-3">Author: <?php echo $post['username'] ?></p>
      <?php if (count($genres) > 0): ?>
        <p class="mt-2 text-secondary">Genre: <?php echo implode(", ", $genres); ?></p>
      <?php endif; ?>
      <p class="mt-3 text-secondary">Synopsis</p> 
      <small class="container text-secondary"><?php echo $post['synopsis'] ?></small> 
      <hr>
      <small class="mt-3"><?php echo $post['content'] ?></small></br>
      <div class="mb-5"></div>
      <a class="text-decoration-none" href="index.php">Back to home</a>
      <div class="mt-5"></div>
    </div>
  </body>
</html>
