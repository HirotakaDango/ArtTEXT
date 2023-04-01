<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}
if (isset($_POST['submit'])) {
  $title = htmlspecialchars($_POST['title']);
  $content = htmlspecialchars($_POST['content']);
  $synopsis = htmlspecialchars($_POST['synopsis']);
  $tags = htmlspecialchars($_POST['tags']);
  $content = nl2br($content);
  $stmt = $db->prepare("INSERT INTO posts (title, content, synopsis, tags, user_id) VALUES (:title, :content, :synopsis, :tags, :user_id)");
  $stmt->execute(array(':title' => $title, ':content' => $content, ':synopsis' => $synopsis, ':tags' => $tags, ':user_id' => $_SESSION['user_id']));
  header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php include('header.php'); ?>
    <form method="post" enctype="multipart/form-data" class="container mt-3">
      <div class="form-floating mb-2">
        <input class="form-control text-secondary fw-bold" type="text" name="title" placeholder="Enter title" maxlength="100" required>  
        <label for="floatingInput" class="text-secondary fw-bold"><small>Enter title</small></label>
      </div>
      <div class="form-floating mb-2">
        <input class="form-control text-secondary fw-bold" type="text" name="tags" placeholder="Enter tag" maxlength="50" required>  
        <label for="floatingInput" class="text-secondary fw-bold"><small>Enter tag</small></label>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control text-secondary fw-bold" style="height: 200px;" type="text" name="synopsis" placeholder="Enter synopsis" maxlength="450" required></textarea>
        <label for="floatingInput" class="text-secondary fw-bold"><small>Enter synopsis</small></label>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control text-secondary fw-bold" style="height: 400px;" name="content" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }" placeholder="Enter content" required></textarea>
        <label for="floatingInput" class="text-secondary fw-bold"><small>Enter content</small></label>
      </div>
      <button class="btn btn-primary fw-bold mb-5" type="submit" name="submit">Submit</button>
    </form>
  </body>
</html>
