<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}

$db = new PDO('sqlite:database.db');
$db->exec("CREATE TABLE IF NOT EXISTS users ( id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL, password TEXT NOT NULL)");
$db->exec("CREATE TABLE IF NOT EXISTS posts ( id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, synopsis TEXT NOT NULL, content TEXT NOT NULL, user_id INTEGER NOT NULL, cover BLOB NOT NULL, tags TEXT NOT NULL, FOREIGN KEY (user_id) REFERENCES users(id))");
$query = "SELECT * FROM posts ORDER BY id DESC";
$posts = $db->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>ArtTEXT</title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php include('header.php'); ?>
    <div class="mt-4">
      <div class="container text-center">
          <div class="contents">
            <?php foreach ($posts as $post): ?>
              <a class="content text-decoration-none" href="view.php?id=<?php echo $post['id'] ?>">
                <div class="card border border-2 h-100">
                  <img class="img-fluid border-bottom lazy-load" alt="cover" style="border-radius: 4px 4px 0 0;" data-src="<?php echo !empty($post['cover']) ? 'cover/'.$post['cover'] : 'cover/bg.png'; ?>">
                  <p class="me-1 ms-1 mt-1 mb-1 text-secondary text-decoration-none fw-bold"><?php echo $post['title'] ?></p>
                </div>
              </a>
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