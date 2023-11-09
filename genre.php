<?php
// connect to the database
$pdo = new PDO('sqlite:database.db');

// set the number of posts per page
$posts_per_page = 100;

// get the tag from the URL parameter
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';

// count the total number of posts with the given tag
$stmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE tags LIKE :tag');
$stmt->bindValue(':tag', '%'.$tag.'%');
$stmt->execute();
$total_posts = $stmt->fetchColumn();

// calculate the total number of pages
$total_pages = ceil($total_posts / $posts_per_page);

// get the current page from the URL parameter
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// calculate the offset
$offset = ($page - 1) * $posts_per_page;

// query the database for the posts on the current page
$stmt = $pdo->prepare('SELECT * FROM posts WHERE tags LIKE :tag ORDER BY id DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':tag', '%'.$tag.'%');
$stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title>Posts by Genre: <?php echo $tag ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="transitions.css" />
    <script type="module" src="swup.js"></script>
  </head>
  <body>
    <main id="swup" class="transition-main">
    <?php include('header.php'); ?>
    <p class="ms-3 mt-3 fw-bold">Genre: <?php echo $tag ?></p> 
    <div class="mt-2">
      <div class="container-fluid text-center">
        <div class="contents">
          <?php foreach ($posts as $post): ?>
            <a class="content text-decoration-none" href="view.php?id=<?php echo $post['id'] ?>">
              <div class="card border border-2 h-100">
                <i class="bi bi-book-half display-1 mt-1 text-secondary border-bottom"></i>
                <p class="me-1 ms-1 mt-1 mb-1 text-secondary text-decoration-none fw-bold"><?php echo $post['title'] ?></p>
              </div>
            </a>
          <?php endforeach ?> 
        </div>
        <div class="pagination mt-4 justify-content-center">
          <?php if ($page > 1): ?>
            <a class="btn btn-sm fw-bold btn-primary me-1" href="?tag=<?php echo $tag ?>&page=<?php echo $page - 1 ?>">Prev</a>
          <?php endif ?>
          <?php if ($page < $total_pages): ?>
            <a class="btn btn-sm fw-bold btn-primary ms-1" href="?tag=<?php echo $tag ?>&page=<?php echo $page + 1 ?>">Next</a>
          <?php endif ?> 
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
    </main>
  </body>
</html>
