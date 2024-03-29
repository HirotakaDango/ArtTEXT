<?php
// connect to the database
$pdo = new PDO('sqlite:database.db');

// set the number of posts per page
$posts_per_page = 10;

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
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <main id="swup" class="transition-main">
    <?php include('header.php'); ?>
    <p class="ms-3 mt-3 fw-bold">Genre: <?php echo $tag ?></p> 
    <div class="container-fluid my-4">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-md-4 g-3">
        <?php foreach ($posts as $post): ?>
          <div class="col">
            <a class="content text-decoration-none" href="view.php?id=<?php echo $post['id'] ?>">
              <div class="card shadow-sm h-100 position-relative">
                <div class="d-flex justify-content-center align-items-center text-center">
                  <i class="bi bi-book-half display-1 p-5 text-secondary border-bottom w-100"></i>
                </div>
                <h5 class="border-bottom text-center w-100 p-3"><?php echo $post['title']; ?></h5>
                <div class="card-body">
                  <?php
                    // Get the full description
                    $fullDesc = $post['synopsis'];

                    // Limit the description to 120 characters (words)
                    $limitedDesc = substr($post['synopsis'], 0, 120);

                    // Find the position of the last word within the first 120 characters
                    $lastSpacePos = strrpos($limitedDesc, ' ');

                    // Check if the full description is longer than the limited description
                    if (strlen($post['synopsis']) > strlen($limitedDesc)) {
                      // If it is, add a "full view" link
                      $limitedDesc = substr($limitedDesc, 0, $lastSpacePos).'...';
                    }
                  ?>
                  <p class="card-text text-start"><?php echo $limitedDesc; ?></p>
                  <br><br>
                  <div class="">
                    <small class="text-body-secondary position-absolute bottom-0 end-0 m-2 fw-medium"><?php echo $post['date']; ?></small>
                  </div>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="pagination my-4 justify-content-center gap-2">
      <?php if ($page > 1): ?>
        <a class="btn btn-sm fw-bold btn-primary" href="?tag=<?php echo $tag; ?>&page=<?php echo $page - 1 ?>">Prev</a>
      <?php endif ?>

      <?php
      $start_page = max(1, $page - 2);
      $end_page = min($total_pages, $page + 2);

      for ($i = $start_page; $i <= $end_page; $i++):
      ?>
        <a class="btn btn-sm fw-bold btn-primary <?php echo ($i == $page) ? 'active' : ''; ?>" href="?tag=<?php echo $tag; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
      <?php endfor ?>

      <?php if ($page < $total_pages): ?>
        <a class="btn btn-sm fw-bold btn-primary" href="?tag=<?php echo $tag; ?>&page=<?php echo $page + 1 ?>">Next</a>
      <?php endif ?>
    </div>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>
