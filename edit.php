<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
  $post_id = $_POST['post_id'];
  $title = htmlspecialchars($_POST['title']);
  $tags = htmlspecialchars($_POST['tags']);
  $synopsis = htmlspecialchars($_POST['synopsis']);
  $content = htmlspecialchars($_POST['content']);
  $content = nl2br($content);
  $query = "UPDATE posts SET title='$title', tags='$tags', synopsis='$synopsis', content='$content' WHERE id='$post_id'";
  $db->exec($query);
  header("Location: profile.php");
}

if (isset($_GET['id'])) {
  $post_id = $_GET['id'];
  $query = "SELECT * FROM posts WHERE id='$post_id' AND user_id='$user_id'";
  $post = $db->query($query)->fetch();
  if (!$post) {
    header("Location: profile.php");
  }
  $tags = htmlspecialchars($post['tags']); // encode tags
} else {
  header("Location: profile.php");
}\

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
    <title>Edit <?php echo $post['title'] ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php include('header.php'); ?>
    <form method="post" class="container-fluid mt-3">
      <input type="hidden" name="post_id" value="<?php echo $post_id ?>">
      <div class="form-floating mb-2">
        <input class="form-control fw-bold" type="text" name="title" placeholder="Enter title" maxlength="100" required value="<?php echo $post['title'] ?>">  
        <label for="floatingInput" class="fw-bold"><small>Enter title</small></label>
      </div>
      <div class="form-floating mb-2">
        <input class="form-control fw-bold" type="text" name="tags" placeholder="Enter genre" maxlength="50" required value="<?php echo $post['tags'] ?>">  
        <label for="floatingInput" class="fw-bold"><small>Enter genre</small></label>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control fw-bold" style="height: 200px;" type="text" name="synopsis" placeholder="Enter synopsis" maxlength="450" required><?php echo $post['synopsis'] ?></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter synopsis</small></label>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control fw-bold" style="height: 650px;" name="content" oninput="stripHtmlTags(this)" placeholder="Enter content" required><?php echo strip_tags($post['content']) ?></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter content</small></label>
      </div>
      <button class="btn btn-primary fw-bold mb-5" type="submit" name="submit">Save Changes</button>
    </form>
  </body>
</html>