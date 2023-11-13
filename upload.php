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
  $synopsis = nl2br($synopsis);
  $date = date('Y/m/d'); // format the current date as "YYYY-MM-DD"
  $stmt = $db->prepare("INSERT INTO posts (title, content, synopsis, tags, user_id, date) VALUES (:title, :content, :synopsis, :tags, :user_id, :date)"); // added the "date" column
  $stmt->execute(array(':title' => $title, ':content' => $content, ':synopsis' => $synopsis, ':tags' => $tags, ':user_id' => $_SESSION['user_id'], ':date' => $date)); // insert the formatted date into the "date" column
  header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title>Upload</title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <main id="swup" class="transition-main">
    <?php include('header.php'); ?>
    <form method="post" enctype="multipart/form-data" class="container-fluid mt-3">
      <div class="input-group gap-3 mb-2">
        <div class="form-floating">
          <input class="form-control fw-bold border-top-0 border-start-0 border-end-0 rounded-bottom-0 border-3 focus-ring focus-ring-dark" type="text" name="title" placeholder="Enter title" maxlength="100" required>  
          <label for="floatingInput" class="fw-bold"><small>Enter title</small></label>
        </div>
        <div class="form-floating">
          <input class="form-control fw-bold border-top-0 border-start-0 border-end-0 rounded-bottom-0 border-3 focus-ring focus-ring-dark" type="text" name="tags" placeholder="Enter genre" maxlength="50" required>  
          <label for="floatingInput" class="fw-bold"><small>Enter genre</small></label>
        </div>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control fw-bold border-top-0 border-start-0 border-end-0 rounded-bottom-0 border-3 focus-ring focus-ring-dark" style="height: 100px;" type="text" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }" name="synopsis" placeholder="Enter synopsis" maxlength="450" required></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter synopsis</small></label>
      </div>
      <div class="form-floating mb-2">
        <textarea class="form-control fw-bold rounded border-3 focus-ring focus-ring-dark" style="height: 650px;" name="content" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }" placeholder="Enter content" required></textarea>
        <label for="floatingInput" class="fw-bold"><small>Enter content</small></label>
      </div>
      <button class="btn btn-primary fw-bold mb-5 w-100" type="submit" name="submit">Submit</button>
    </form>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>
