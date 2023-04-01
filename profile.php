<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
    header('Location: session.php');
}
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id='$user_id'";
$user = $db->query($query)->fetch();
$query = "SELECT * FROM posts WHERE user_id='$user_id' ORDER BY id DESC";
$posts = $db->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $user['username'] ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php include('header.php'); ?>
    <div class="mt-3">
      <h3 class="text-center"><?php echo $user['username'] ?>'s works</h3>
        <div class="container text-center">
          <div class="contents">
            <?php foreach ($posts as $post): ?>
              <div class="content card border border-2 h-100">
                <a href="view.php?id=<?php echo $post['id'] ?>"><img class="img-fluid border-bottom lazy-load" alt="cover" style="border-radius: 4px 4px 0 0;" data-src="<?php echo !empty($post['cover']) ? 'cover/'.$post['cover'] : 'cover/bg.png'; ?>"></a>
                <a class="me-1 ms-1 mt-1 mb-1 text-secondary text-decoration-none fw-bold" href="view.php?id=<?php echo $post['id'] ?>"><?php echo $post['title'] ?></a>
                <header class="d-flex justify-content-center py-3">
                  <ul class="nav nav-pills">
                    <li class="nav-item"><a class="btn btn-sm btn-danger me-1" href="delete.php?id=<?php echo $post['id'] ?>" onclick="return confirm('Are you sure?')"><i class="bi bi-trash-fill"></i></a></li>
                    <li class="nav-item"><a class="btn btn-sm btn-primary ms-1" href="edit.php?id=<?php echo $post['id'] ?>"><i class="bi bi-pencil-fill"></i></a></li>
                  </ul>
                </header>
              </div>
            <?php endforeach ?> 
        </div>
      </div>
    </div> 
    <style>
      .contents {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) /* Two columns with a minimum width of 300px */;
        grid-gap: 10px;
        justify-content: center;
        margin-right: 1px;
        margin-left: 1px;
      }
    </style> 
    <script>
        document.addEventListener("DOMContentLoaded", function() {
          let lazyloadImages;
          if("IntersectionObserver" in window) {
            lazyloadImages = document.querySelectorAll(".lazy-load");
            let imageObserver = new IntersectionObserver(function(entries, observer) {
              entries.forEach(function(entry) {
                if(entry.isIntersecting) {
                  let image = entry.target;
                  image.src = image.dataset.src;
                  image.classList.remove("lazy-load");
                  imageObserver.unobserve(image);
                }
              });
            });
            lazyloadImages.forEach(function(image) {
              imageObserver.observe(image);
            });
          } else {
            let lazyloadThrottleTimeout;
            lazyloadImages = document.querySelectorAll(".lazy-load");

            function lazyload() {
              if(lazyloadThrottleTimeout) {
                clearTimeout(lazyloadThrottleTimeout);
              }
              lazyloadThrottleTimeout = setTimeout(function() {
                let scrollTop = window.pageYOffset;
                lazyloadImages.forEach(function(img) {
                  if(img.offsetTop < (window.innerHeight + scrollTop)) {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-load');
                  }
                });
                if(lazyloadImages.length == 0) {
                  document.removeEventListener("scroll", lazyload);
                  window.removeEventListener("resize", lazyload);
                  window.removeEventListener("orientationChange", lazyload);
                }
              }, 20);
            }
            document.addEventListener("scroll", lazyload);
            window.addEventListener("resize", lazyload);
            window.addEventListener("orientationChange", lazyload);
          }
        })
    </script>
  </body>
</html>