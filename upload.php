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
  $errors = array();

  if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $extensions = array("jpeg","jpg","png");
    $tmp = explode('.', $file_name);
    $file_ext = strtolower(end($tmp));
    if(in_array($file_ext,$extensions)=== false){
      $errors[]="extension not allowed, please choose a JPEG or PNG file.";
    }
    if($file_size > 2097152){
      $errors[]='File size must be less than 2 MB';
    }
  } else {
    $file_name = null;
  }

  if(empty($errors)){
    if($file_name !== null){
      list($width, $height) = getimagesize($file_tmp);
      $aspect_ratio = $width / $height;
      $new_width = 200;
      $new_height = round($new_width / $aspect_ratio);
      $image_p = imagecreatetruecolor($new_width, $new_height);
      if($file_ext == 'jpeg' || $file_ext == 'jpg'){
        $image = imagecreatefromjpeg($file_tmp);
      }
      elseif($file_ext == 'png'){
        $image = imagecreatefrompng($file_tmp);
      }
      imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
      $file_name = uniqid().'.'.$file_ext;
      imagejpeg($image_p, "cover/".$file_name);
      imagedestroy($image_p);
    }

    $stmt = $db->prepare("INSERT INTO posts (title, content, synopsis, cover, tags, user_id) VALUES (:title, :content, :synopsis, :cover, :tags, :user_id)");
    $stmt->execute(array(':title' => $title, ':content' => $content, ':synopsis' => $synopsis, ':cover' => $file_name, ':tags' => $tags, ':user_id' => $_SESSION['user_id']));
    header('Location: index.php');
  }
  else{
    print_r($errors);
  }
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
      <img id="file-ip-1-preview" style="height: 250px; width: 100%; margin-bottom: 15px; object-fit: cover;">
      <input class="form-control mb-2 text-secondary fw-bold" type="file" name="image" type="file" id="file-ip-1" accept="image/*" onchange="showPreview(event);">
      <div class="form-floating mb-2">
        <input class="form-control text-secondary fw-bold" type="text" name="title" placeholder="Enter title" maxlength="100" required>  
        <label for="floatingInput" class="text-secondary fw-bold"><small>Enter title</small></label>
      </div>
      <div class="form-floating mb-2">
        <input class="form-control border rounded-3 text-secondary fw-bold border-4" type="text" name="tags" placeholder="Enter tag" maxlength="50" required>  
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
    <script>
      function showPreview(event){
        if(event.target.files.length > 0){
          var src = URL.createObjectURL(event.target.files[0]);
          var preview = document.getElementById("file-ip-1-preview");
          preview.src = src;
          preview.style.display = "block";
        }
      }
    </script>
  </body>
</html>
