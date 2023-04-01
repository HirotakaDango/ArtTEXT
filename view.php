<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}
$id = $_GET['id'];
$query = "SELECT * FROM posts WHERE id='$id'";
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
      <center><img class="img-fluid border border-3 rounded-3 mb-2" alt="cover" src="<?php echo !empty($post['cover']) ? 'cover/'.$post['cover'] : 'cover/bg.png'; ?>"></center>
      <h1 class="text-center fw-semibold"><?php echo $post['title'] ?></h1>
      <?php if (count($genres) > 0): ?>
        <p class="mt-2 text-secondary">Genre:<small class="container text-secondary fw-bold"><?php echo implode(", ", $genres); ?></small></p>
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
