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
  $synopsis = nl2br($synopsis);
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
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title>Edit <?php echo $post['title'] ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <main id="swup" class="transition-main">
    <?php include('header.php'); ?>
    <form method="post" class="container-fluid my-4">
      <div class="d-none d-md-block d-lg-block">
        <div class="d-flex">
          <button class="me-auto btn btn-primary fw-bold mb-5" type="submit" name="submit">save changes</button>
          <a class="ms-auto btn btn-primary fw-bold mb-5" href="profile.php">back to profile</a>
        </div>
      </div>
      <input type="hidden" name="post_id" value="<?php echo $post_id ?>">
      <div class="input-group gap-3 mb-2">
        <div class="form-floating">
          <input class="form-control border-top-0 border-start-0 border-end-0 rounded-bottom-0 border-3 focus-ring focus-ring-dark" type="text" name="title" placeholder="Enter title" maxlength="100" required value="<?php echo $post['title'] ?>">  
          <label for="floatingInput" class="fw-bold"><small>Enter title</small></label>
        </div>
        <div class="form-floating">
          <input class="form-control border-top-0 border-start-0 border-end-0 rounded-bottom-0 border-3 focus-ring focus-ring-dark" type="text" name="tags" placeholder="Enter genre" maxlength="50" required value="<?php echo $post['tags'] ?>">  
          <label for="floatingInput" class="fw-bold"><small>Enter genre</small></label>
        </div>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control border-top-0 border-start-0 border-end-0 rounded-bottom-0 border-3 focus-ring focus-ring-dark" style="height: 250px;" type="text" name="synopsis" oninput="stripHtmlTags(this)" placeholder="Enter synopsis" maxlength="450" required><?php echo strip_tags($post['synopsis']) ?></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter synopsis</small></label>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control rounded border-3 focus-ring focus-ring-dark vh-100" name="content" oninput="stripHtmlTags(this)" placeholder="Enter content" required><?php echo strip_tags($post['content']) ?></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter content</small></label>
      </div>
      <div class="d-flex d-md-none d-lg-none">
        <button class="me-auto btn btn-primary fw-bold mb-5" type="submit" name="submit">save changes</button>
        <a class="ms-auto btn btn-primary fw-bold mb-5" href="profile.php">back to profile</a>
      </div>
    </form>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>